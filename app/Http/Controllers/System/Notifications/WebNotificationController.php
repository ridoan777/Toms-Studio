<?php

namespace App\Http\Controllers\System\Notifications;

use App\Helpers\Errors\LoggerAccess;
use App\Helpers\IconPack;
use App\Helpers\Ui\DatatableHelper;
use App\Http\Controllers\Controller;
use App\Models\System\Notifications\InAppNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\{Auth, DB, Log, Storage};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class WebNotificationController extends Controller
{
	public function index()
	{
		return view('Admin.sidebar.System.notifications.index');
	}


	public function toggle(InAppNotification $notify)
	{
		try {
			$notify->update([
				'status' => !$notify->status,
			]);

			return response()->json(['success' => true]);
		} catch (Exception $e) {
			return response()->json(['success' => false, 'error' => $e->getMessage()]);
		}
	}


	public function delete(InAppNotification $notify)
	{
		$name = null;
		try {
			$name = ($notify?->type ?? '') . ' ' . ($notify?->severity ?? '') . " of id:" . ($notify?->id ?? '');
			$notify->delete();

			return redirect()->back()->with('success', "A notification '{$name}' has been deleted successfully.");
		} catch (Exception $e) {
			return redirect()->back()->with('error', 'Action Error: ' . $e->getMessage())->withInput();
		}
	}

	public function bulkDelete(Request $request)
	{
		$request->validate([
			'ids' => 'required|array',
			'ids.*' => 'integer|exists:in_app_notifications,id',
		]);

		$ids = $request->ids;

		$notifys = InAppNotification::whereIn('id', $ids)->get();

		if ($notifys->isEmpty()) {
			return back()->with('error', 'No valid notifications selected.');
		}

		$MESSAGE = "Selected notifications are deleted.";
		$deleted = [];

		DB::beginTransaction();

		try {
			foreach ($notifys as $notify) {
				$name = ($notify?->type ?? '') . ' ' . ($notify?->severity ?? '') . " of id:" . ($notify?->id ?? '');
				$deleted[] = $notify->name;
				$notify->delete();
			}
			DB::commit();
			$MESSAGE = $MESSAGE . "success: [" . count($deleted) . "]";

			return redirect()->back()->with('success', $MESSAGE);
		} catch (Throwable $e) {
			DB::rollBack();
			Log::error('Notifications bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);
			return back()->with('error', 'Bulk delete failed. No changes were made.');
		}
	}

	public function inAppNotifyDatatable(Request $request)
	{
		try {
			$columns = [null, null, 'id', 'trigger_place', 'type', null, null, 'message', null, 'status', 'created_at', null];

			$draw = intval($request->input('draw'));
			$start = intval($request->input('start', 0));
			$length = intval($request->input('length', 25));

			$orderColIndex = (int)$request->input('order.0.column', 10);
			$orderCol = $columns[$orderColIndex] ?? 'created_at';
			$orderCol = $orderCol ?: 'created_at';

			$orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

			$query = InAppNotification::with('inAppNotifyRelatingBackTo_User')
				->select(['id', 'trigger_place', 'type', 'severity', 'user_id', 'focus_name', 'focus_image', 'title', 'message', 'action_url', 'action_label', 'status', 'created_at']);

			if ($search = $request->input('search.value')) {
				$query->where(function ($q) use ($search) {
					$q->where('message', 'like', "%{$search}%")
						->orWhere('trigger_place', 'like', "%{$search}%")
						->orWhere('type', 'like', "%{$search}%")
						->orWhere('severity', 'like', "%{$search}%")
						->orWhere('title', 'like', "%{$search}%")
						->orWhere('focus_name', 'like', "%{$search}%")
						->orWhere('action_label', 'like', "%{$search}%")
						->orWhere('user_id', 'like', "%{$search}%")
						->orWhereHas('inAppNotifyRelatingBackTo_User', function ($q) use ($search) {
							$q->where(function ($sub) use ($search) {
								foreach (['id', 'user_uid', 'username', 'name', 'email', 'phone'] as $field) {
									$sub->orWhere($field, 'like', "%{$search}%");
								}
							});
						});
				});
			}

			$recordsTotal = InAppNotification::count();
			$recordsFiltered = $query->count();

			$data = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

			$sn = $start;
			// timezone
			$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

			$data->transform(function ($row) use (&$sn, $zones) {

				$row->DT_RowId = 'row_' . $row->id;

				// clickable row
				$row->view_url = $row->action_url ?? route('dashboard');

				// bulk checkbox
				$row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';

				$row->SN = ++$sn;

				// ---------------- USER FIELD ----------------
				$row->user = DatatableHelper::userBlock($row->inAppNotifyRelatingBackTo_User);

				// ---------------- TYPE + SEVERITY ----------------
				$row->type = '
					<div class="flex flex-col text-center leading-tight">
						<span class="my-1">' . DatatableHelper::notifyColor($row->type) . '</span>
						<span class="my-1 text-gray-200">' . DatatableHelper::notifyColor($row->severity) . '</span>
					</div>
            ';

				// ---------------- FOCUS FIELD ----------------
				if ($row->focus_image) {
					$focusAvatar = $row->focus_image;
					$focusHtml = '<img src="' . $focusAvatar . '" class="w-10 h-10 rounded-lg object-cover mx-auto" />';
				} else {
					$focusHtml = '<span class="text-gray-400 italic">N/A</span>';
				}

				$row->focus = '
                <div class="min-w-40 flex items-center space-x-3 flex-nowrap">
                    <div class="shrink-0">' . $focusHtml . '</div>
                    <div class="flex flex-col text-left">
                        <span class="text-sm font-medium">' . e($row->focus_name ?? '—') . '</span>
                    </div>
                </div>
            ';

				// ---------------- MESSAGE ----------------
				$row->message = '<div class="min-w-32">' . Str::limit(strip_tags($row->message), 100, '...') . '</div>';

				// ---------------- ACTION LABEL ----------------
				$row->action = $row->action_label
					? '<span class="font-medium">' . e($row->action_label) . '</span>'
					: '<span class="text-gray-400 italic">N/A</span>';

				// ------------- Created date -------------
				$row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

				$row->actions = DatatableHelper::datatableAction($row, true, 'backend_system_in_app_notify_toggle', 'backend_system_in_app_notify_delete');

				return $row;
			});

			return response()->json([
				'draw' => $draw,
				'recordsTotal' => $recordsTotal,
				'recordsFiltered' => $recordsFiltered,
				'data' => $data,
			]);
		} catch (Exception $e) {
			return DatatableHelper::handleDatatableError($request, $e, true, "Notification datatable error");
		}
	}
}
