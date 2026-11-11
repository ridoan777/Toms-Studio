<?php
namespace App\Http\Controllers\System\Dispute_Management;

use App\Helpers\IconPack;
use App\Helpers\System\ReportDeleteBlock;
use App\Http\Controllers\Controller;
use App\Helpers\Notifications\InAppNotificationHelper;
use App\Models\Communication\Chatting\ChatMessage;
use App\Models\System\Dispute_Management\DisputeReport;
use App\Models\Users\{User, UserAccess, UserVerification};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Artisan, Auth, Cache, DB, Log, Storage};
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class WebDisputeManagementController extends Controller
{
	public function index()
	{
		return view('Admin.sidebar.Dispute_managements.index');
	}


	public function show(DisputeReport $report)
	{
		$userAccess = UserAccess::userId($report->accused_id)->first();

		$report->load([
			'disputeReport' => function ($q) {
				if ($q->getModel() instanceof ChatMessage) {
					$q->with('chatMessageRelationWith_Gallery');
				}
			},
			'victimRelationWith_User',
			'accusedRelationWith_User'
		]);

		return view('Admin.sidebar.Dispute_managements.show', compact('report', 'userAccess'));
	}

	public function toggle(DisputeReport $report)
	{
		try {
			$report->update([
				'status' => !$report->status,
			]);

			return response()->json(['success' => true]);
		} catch (Exception $e) {
			return response()->json(['success' => false, 'error' => $e->getMessage()]);
		}
	}

	public function delete(DisputeReport $report)
	{
		$victim = null;
		$accused = null;
		try {
			$victim = $report->victimRelationWith_User->name ?? null;
			$accused = $report->accusedRelationWith_User->name ?? null;
			$section = $report->issue_section ?? null;

			$report->delete();

			return redirect()->back()->with('success', "Report from section:'{$section}' sent by '{$victim}' against '{$accused}' has been deleted successfully. This will not lift existing penalties.");
		} catch (Exception $e) {
			return redirect()->back()->with('error', 'Action Error: ' . $e->getMessage())->withInput();
		}
	}

	public function reportsDatatable(Request $request)
	{
		try {
			$columns = ['id', 'dispute_report_type', 'dispute_report_id', 'issue_section', 'victim_id', 'accused_id', 'original_content', 'issue_label', 'description', 'is_resolved', 'status', 'created_at'];

			$draw = (int)$request->input('draw');
			$start = (int)$request->input('start', 0);
			$length = (int)$request->input('length', 25);
			$orderColIndex = (int)$request->input('order.0.column', 0);
			$orderCol = $columns[$orderColIndex] ?? 'id';
			$orderDir = in_array($request->input('order.0.dir'), ['asc', 'desc']) ? $request->input('order.0.dir') : 'asc';

			$query = DisputeReport::with(['victimRelationWith_User', 'accusedRelationWith_User'])
				->select(['id', 'dispute_report_type', 'dispute_report_id', 'issue_section', 'victim_id', 'accused_id', 'original_content', 'issue_label', 'description', 'is_resolved', 'status', 'created_at']);

			if ($search = $request->input('search.value')) {
				$query->where(function ($q) use ($search) {
					$q
						->where('issue_label', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%")
						->orWhere('dispute_report_id', 'like', "%{$search}%")
						->orWhere('dispute_report_type', 'like', "%{$search}%")
						->orWhere('original_content', 'like', "%{$search}%")
						->orWhere('is_resolved', 'like', "%{$search}%")
						->orWhere('status', 'like', "%{$search}%");
				});
			}

			$recordsTotal = DisputeReport::count();
			$recordsFiltered = $query->count();

			$data = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();
			$sn = $start;

			$data->transform(function ($row) use (&$sn) {
				$row->DT_RowId = 'row_' . $row->id;
				$row->view_url = route('backend_displute_report_show', $row->id);
				$row->SN = ++$sn;

				$row->section = $row->issue_section;
				$row->content = '<span class="text-gray-400 dark:text-gray-600">[ ' . $row->dispute_report_id . ' ] </span>' . Str::limit($row->original_content ?? '—', 30);

				$victim = $row->victimRelationWith_User;
				$accused = $row->accusedRelationWith_User;

				$row->victim = '
					<div class="flex items-center space-x-3">
						<img src="' . ($victim->avatar ? Storage::url($victim->avatar) : asset('site_assets/dummies/dummy_man.webp')) . '" class="w-10 h-10 rounded-full object-cover" />

						<div>
							<div class="text-xs text-gray-400">DB ID: ' . $victim->id . '</div>
							<div class="text-sm font-medium">' . e($victim->name) . '</div>
							<div class="text-xs text-gray-400">' . e($victim->email) . '</div>
						</div>
					</div>';

				$row->accused = '
					<div class="flex items-center space-x-3">
						<img src="' . ($accused->avatar ? Storage::url($accused->avatar) : asset('site_assets/dummies/dummy_man.webp')) . '" class="w-10 h-10 rounded-full object-cover" />
						<div>
							<div class="text-xs text-gray-400">DB ID: ' . $accused->id . '</div>
							<div class="text-sm font-medium">' . e($accused->name) . '</div>
							<div class="text-xs text-gray-400">' . e($accused->email) . '</div>
						</div>
					</div>';

				$row->phase = $row->is_resolved
					? '<span class="px-3 py-1 rounded-lg bg-green-400 bg-opacity-25 text-xs">Resolved</span>'
					: '<span class="px-3 py-1 rounded-lg bg-orange-300 bg-opacity-25 text-xs">Disputed</span>';

				$row->issue_label = e($row->issue_label);
				$row->description = Str::limit($row->description ?? '-', 30);

				$row->status_label = $row->status
					? '<span class="text-green-600 font-medium">Active</span>'
					: '<span class="text-red-600 font-medium">Inactive</span>';

				$row->actions = '
					<div class="flex justify-center items-center space-x-2">
					
						<form action="' . route('backend_displute_report_toggle', $row->id) . '" method="POST" class="toggleForm">
							' . csrf_field() . '
							<button type="submit" class="text-white p-2 rounded">
								' . ($row->status
					? IconPack::toggleOn(['class' => 'iconPackItem w-8 h-8 text-green-500'])
					: IconPack::toggleOff(['class' => 'iconPackItem w-8 h-8 text-red-500']))
					. '
							</button>
						</form>

						<form action="' . route('backend_displute_report_delete', $row->id) . '" method="POST" class="deleteForm">
							' . csrf_field() . method_field('DELETE') . '
							<button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded">
								' . IconPack::trash(['class' => 'iconPackItem w-3 h-3 text-gray-100']) . '
							</button>
						</form>
					</div>
				';

				$row->created_at_formatted = $row->created_at?->format('d M Y, H:i') ?? 'N/A';

				return $row;
			});

			return response()->json([
				'draw' => $draw,
				'recordsTotal' => $recordsTotal,
				'recordsFiltered' => $recordsFiltered,
				'data' => $data,
			]);
		} catch (Throwable $e) {
			Log::error('Chat report datatable error', ['error' => $e->getMessage()]);
			return response()->json([
				'draw' => $draw,
				'recordsTotal' => 0,
				'recordsFiltered' => 0,
				'data' => [],
				'error' => config('app.debug') ? $e->getMessage() : 'An error occurred.',
			], 500);
		}
	}
	// ---------------------- SOLUTIONS ------------------------

	public function resolution(Request $request, DisputeReport $report)
	{
		// dd($request->all());
		try {
			$validated = $request->validate([
				'id' => 'nullable|integer|exists:user_accesses,id',
				'user_id' => 'nulalble|integer|exists:users,id',
				'is_resolved' => 'nullable|boolean|in:0,1',
				'status' => 'nullable|boolean|in:0,1',

				'delete_content' => 'nullable',
				'issue_section' => 'nullable|string|exists:dispute_reports,issue_section',

				'join_ban' => 'nullable|boolean|in:0,1',
				'chat_ban' => 'nullable|boolean|in:0,1',
				'gallery_ban' => 'nullable|boolean|in:0,1',
				'store_ban' => 'nullable|boolean|in:0,1',
				'view_ban' => 'nullable|boolean|in:0,1',
				'account_hold' => 'nullable|boolean|in:0,1',

				'message_victim' => 'nullable|string|max:2000',
				'message_accused' => 'nullable|string|max:2000',
				'admin_note' => 'nullable|string|max:2000',

				'ban_expiry' => [
					'nullable',
					'date',
					Rule::requiredIf(function () use ($request) {
						return collect(['join_ban','chat_ban','gallery_ban','store_ban','view_ban','account_hold'])->contains(fn($field) => $request->boolean($field));
					}),
				],
			],[
				'ban_expiry.required' => "You must set a Ban expiry date (Ban Until)"
			]);

			$ADMIN = $request->user();
			$VICTIM = User::findOrFail($report?->victim_id);
			$ACCUSED = User::findOrFail($report?->accused_id);
			$userAccess = UserAccess::updateOrCreate(
				[
					'user_id' => $ACCUSED->id ?? null,
				],
				[
					'email' => $ACCUSED->email ?? null,
					'user_role' => $ACCUSED->user_role ?? "unknwon-role",

					'join_ban' => isset($validated['join_ban']) ? (int)$validated['join_ban'] : 0,
					'chat_ban' => isset($validated['chat_ban']) ? (int)$validated['chat_ban'] : 0,
					'gallery_ban' => isset($validated['gallery_ban']) ? (int)$validated['gallery_ban'] : 0,
					'store_ban' => isset($validated['store_ban']) ? (int)$validated['store_ban'] : 0,
					'view_ban' => isset($validated['view_ban']) ? (int)$validated['view_ban'] : 0,
					'account_hold' => isset($validated['account_hold']) ? (int)$validated['account_hold'] : 0,

					'admin_note' => $validated['admin_note'] ?? null,
					'ban_expiry' => $validated['ban_expiry'] ?? null,

					'status' => $validated['status'] ?? 1,
				]
			);

			$report->update([
				'message_victim' => $validated['message_victim'] ?? null,
				'message_accused' => $validated['message_accused'] ?? null,
				'is_resolved' => (int)$validated['is_resolved'] ?? 0,
			]);

			// ------------------------- LIVE ACTION -------------------------
			if (isset($validated['delete_content']) && $validated['delete_content']) {
				ReportDeleteBlock::deleteChatMessage($report->disputeReport);
				// $report->disputeReport()->delete();
			}
			if (isset($validated['account_hold']) && $validated['account_hold']) {
				$ACCUSED->status = 0;
				$ACCUSED->save();
			}
			// ------------------------- LIVE ACTION -------------------------

			// ------------------------- IN-APP NOTIFICATIONS -------------------------
			if (isset($validated['ban_expiry']) && $validated['ban_expiry']) {
				$MESSAGE_VICTIM = isset($validated['message_victim']) && $validated['message_victim'] ? $validated['message_victim'] : "Based on your report, action has been taken against {$ACCUSED->name} for violating {$validated['issue_section']}!";

				$MESSAGE_ACCOUSED = isset($validated['message_accused']) && $validated['message_accused'] ? $validated['message_accused'] : "Due to your misconduct/misuse in {$validated['issue_section']}, you are restricted to use some feature(s) until {$validated['ban_expiry']}!";

				InAppNotificationHelper::createInAppNotify($VICTIM, $validated['issue_section'], "dispute", "alert", $MESSAGE_VICTIM, $ACCUSED->name, $ACCUSED->avatar_url, null, null, true);
				InAppNotificationHelper::createInAppNotify($ACCUSED, $validated['issue_section'], "dispute", "warning", $MESSAGE_ACCOUSED, $ACCUSED->name, $ACCUSED->avatar_url, null, null, true);
			} else {
				$MESSAGE_VICTIM = isset($validated['message_victim']) && $validated['message_victim'] ? $validated['message_victim'] : "We reviewed your report against {$ACCUSED->name} for violating {$validated['issue_section']}! However, we found that the person did not violate the regulation. Thank you for taking time in this feedback.";

				InAppNotificationHelper::createInAppNotify($VICTIM, $validated['issue_section'], "dispute", "alert", $MESSAGE_VICTIM, $ACCUSED->name, $ACCUSED->avatar_url, null, null, true);
			}
			// ------------------------- IN-APP NOTIFICATIONS -------------------------

			return redirect()->back()->with('success', "User access for '{$ACCUSED->name}' has been changed successfully.");
		} catch (Throwable $e) {
			Log::error('Report handling error', ['error' => $e->getMessage()]);
			return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
		}
	}
}