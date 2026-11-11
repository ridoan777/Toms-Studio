<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\{User, UserVerification};
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Storage};
use Illuminate\Http\Request;
use App\Helpers\Ui\DatatableHelper;
use Carbon\Carbon;
use Throwable;

class WebUserDatatableController extends Controller
{
	// ---------------USERS---------------

	public function all_users_dataTable(Request $request)
	{
		try {
			$columns = ['id', 'user_uid', 'created_by', 'username', 'avatar', 'name', 'user_role', 'email', 'phone', 'guardian', 'position', 'created_at', 'status'];

			$draw = (int)$request->input('draw');
			$start = (int)$request->input('start', 0);
			$length = (int)$request->input('length', 25);
			$orderColIndex = (int)$request->input('order.0.column', 0);
			$orderCol = $columns[$orderColIndex] ?? 'id';
			$orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

			$query = User::select($columns)->with(['roles:id,name,role_key', 'userRelatingBackTo_Instrument']);

			if ($search = $request->input('search.value')) {
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%")
						->orWhere('phone', 'like', "%{$search}%")
						->orWhere('user_uid', 'like', "%{$search}%")
						->orWhere('guardian', 'like', "%{$search}%")
						->orWhere('position', 'like', "%{$search}%")
						->orWhereHas('roles', function ($q) use ($search) {
							$q->where('name', 'like', "%{$search}%");
						});
				});
			}

			$recordsTotal = User::count();
			$recordsFiltered = $query->count();

			$users = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

			$sn = $start;
			$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

			/** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Users\User> $users */
			$users->transform(function ($row) use (&$sn, $zones) {

				$row->DT_RowId = 'row_' . $row->id;
				$row->view_url = route('backend_single_user', $row->id); // clickable row
				$row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';

				$row->SN = ++$sn;

				$row->unique_ids = '<div>'
					. '<p class="text-xs">' . $row->user_uid . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->username ?? "N/A") . '</p>'
					. '</div>';

				// ------------- Avatar -------------
				$userAvatar = $row->avatar ? Storage::url($row->avatar) : asset('site_assets/dummies/dummy_man.webp');
				$row->avatar_img = '<img src="' . $userAvatar . '" class="w-12 h-12 rounded-lg mx-auto">';

				// ------------- User name -------------
				$row->name = '<div>'
					. '<p class="text-sm">' . $row->name . '</p>'
					. '</div>';

				// ------------- Email + Phone -------------
				$row->email = '<div>'
					. '<p class="text-sm">' . $row->email . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->phone ?? "N/A") . '</p>'
					. '</div>';

				// ------------- Position -------------
				$row->position_name = $row->position ? $row?->userRelatingBackTo_Instrument?->name : null;

				// ------------- Created date -------------
				$row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

				// ------------- Role badge -------------
				$row->role = '<div class="flex flex-col items-center justify-center gap-1 text-center"> 
					<p class="my-1 px-2 text-xs text-gray-500 dark:text-gray-200 border rounded text-center">' . $row->roles->pluck('name')->implode(', ') . '</p>
					<p class="text-sm">' . DatatableHelper::roleColor($row->roles->pluck('role_key')->first()) . '</p>'
				;

				$row->actions = DatatableHelper::datatableAction($row, true, 'backend_user_toggle', 'backend_user_delete');

				return $row;
			});

			return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $users);
		} catch (Throwable $e) {
			return DatatableHelper::handleDatatableError($request, $e, true, "All users datatable error");
		}
	}
	// ---------------USERS---------------


	// --------------- ADMINS ---------------

	public function admin_users_dataTable(Request $request)
	{
		try {
			$columns = ['id', 'user_uid', 'created_by', 'username', 'avatar', 'name', 'user_role', 'email', 'phone', 'guardian', 'position', 'created_at', 'status'];

			$draw = (int)$request->input('draw');
			$start = (int)$request->input('start', 0);
			$length = (int)$request->input('length', 25);
			$orderColIndex = (int)$request->input('order.0.column', 0);
			$orderCol = $columns[$orderColIndex] ?? 'id';
			$orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

			$query = User::select($columns)->with('roles:id,name,role_key')->whereHas('roles', function ($q) {
				$q->whereIn('role_key', ['admin', 'super_admin']);
			});

			if ($search = $request->input('search.value')) {
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%")
						->orWhere('user_uid', 'like', "%{$search}%")
						->orWhereHas('roles', function ($q) use ($search) {
							$q->where('name', 'like', "%{$search}%");
						});
				});
			}

			$recordsTotal = User::count();
			$recordsFiltered = $query->count();

			$users = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

			$sn = $start;
			$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

			$users->transform(function ($row) use (&$sn, $zones) {

				$row->DT_RowId = 'row_' . $row->id;
				$row->view_url = route('backend_single_user', $row->id); // clickable row
				$row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';

				$row->SN = ++$sn;

				$row->unique_ids = '<div>'
					. '<p class="text-xs">' . $row->user_uid . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->username ?? "N/A") . '</p>'
					. '</div>';

				// ------------- Avatar -------------
				$userAvatar = $row->avatar ? Storage::url($row->avatar) : asset('site_assets/dummies/dummy_man.webp');
				$row->avatar_img = '<img src="' . $userAvatar . '" class="w-12 h-12 rounded-lg mx-auto">';

				// ------------- User name -------------
				$row->name = '<div>'
					. '<p class="text-sm">' . $row->name . '</p>'
					. '</div>';

				// ------------- Email + Phone -------------
				$row->email = '<div>'
					. '<p class="text-sm">' . $row->email . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->phone ?? "N/A") . '</p>'
					. '</div>';
				
				// ------------- Position -------------
				$row->position_name = $row->position ? $row?->userRelatingBackTo_Instrument?->name : null;

				// ------------- Created date -------------
				$row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

				// ------------- Role badge -------------
				$row->role = '<div class="flex flex-col items-center justify-center gap-1 text-center"> 
					<p class="my-1 px-2 text-xs text-gray-500 dark:text-gray-200 border rounded text-center">' . $row->roles->pluck('name')->implode(', ') . '</p>
					<p class="text-sm">' . DatatableHelper::roleColor($row->roles->pluck('role_key')->first()) . '</p>
				</div>';

				$row->actions = DatatableHelper::datatableAction($row, true, 'backend_user_toggle', 'backend_user_delete');

				return $row;
			});

			return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $users);
		} catch (Throwable $e) {
			return DatatableHelper::handleDatatableError($request, $e, true, "All users datatable error");
		}
	}
	// --------------- ADMINS ---------------


	// --------------- ROLES ---------------

	public function roled_users_dataTable(Request $request, $customRole)
	{
		try {
			$columns = ['id', 'user_uid', 'created_by', 'username', 'avatar', 'name', 'user_role', 'email', 'phone', 'guardian', 'position', 'created_at', 'status'];

			$draw = (int)$request->input('draw');
			$start = (int)$request->input('start', 0);
			$length = (int)$request->input('length', 25);
			$orderColIndex = (int)$request->input('order.0.column', 0);
			$orderCol = $columns[$orderColIndex] ?? 'id';
			$orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
			
			$query = User::select($columns)->with('roles:id,name,role_key')->whereHas('roles', function ($q) use($customRole) {
				$q->where('role_key', $customRole);
			});

			if ($search = $request->input('search.value')) {
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%")
						->orWhere('user_uid', 'like', "%{$search}%")
						->orWhereHas('roles', function ($q) use ($search) {
							$q->where('name', 'like', "%{$search}%");
						});
				});
			}

			$recordsTotal = User::count();
			$recordsFiltered = $query->count();

			$users = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

			$sn = $start;
			$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

			$users->transform(function ($row) use (&$sn, $zones) {

				$row->DT_RowId = 'row_' . $row->id;
				$row->view_url = route('backend_single_user', $row->id); // clickable row
				$row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';

				$row->SN = ++$sn;

				$row->unique_ids = '<div>'
					. '<p class="text-xs">' . $row->user_uid . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->username ?? "N/A") . '</p>'
					. '</div>';

				// ------------- Avatar -------------
				$userAvatar = $row->avatar ? Storage::url($row->avatar) : asset('site_assets/dummies/dummy_man.webp');
				$row->avatar_img = '<img src="' . $userAvatar . '" class="w-12 h-12 rounded-lg mx-auto">';

				// ------------- User name -------------
				$row->name = '<div>'
					. '<p class="text-sm">' . $row->name . '</p>'
					. '</div>';

				// ------------- Email + Phone -------------
				$row->email = '<div>'
					. '<p class="text-sm">' . $row->email . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->phone ?? "N/A") . '</p>'
					. '</div>';
				
				// ------------- Position -------------
				$row->position_name = $row->position ? $row?->userRelatingBackTo_Instrument?->name : null;

				// ------------- Created date -------------
				$row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

				// ------------- Role badge -------------
				$row->role = '<div class="flex flex-col items-center justify-center gap-1 text-center"> 
					<p class="my-1 px-2 text-xs text-gray-500 dark:text-gray-200 border rounded text-center">' . $row->roles->pluck('name')->implode(', ') . '</p>
					<p class="text-sm">' . DatatableHelper::roleColor($row->roles->pluck('role_key')->first()) . '</p>
				</div>';

				$row->actions = DatatableHelper::datatableAction($row, true, 'backend_user_toggle', 'backend_user_delete');

				return $row;
			});

			return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $users);
		} catch (Throwable $e) {
			return DatatableHelper::handleDatatableError($request, $e, true, "All users datatable error");
		}
	}
	// --------------- ROLES ---------------


	// --------------- BLACKLISTS ---------------

	public function blacklisted_users_dataTable(Request $request)
	{
		try {
			$columns = ['id', 'user_uid', 'created_by', 'username', 'avatar', 'name', 'user_role', 'email', 'phone', 'guardian', 'position', 'created_at', 'status'];

			$draw = (int)$request->input('draw');
			$start = (int)$request->input('start', 0);
			$length = (int)$request->input('length', 25);
			$orderColIndex = (int)$request->input('order.0.column', 0);
			$orderCol = $columns[$orderColIndex] ?? 'id';
			$orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

			$query = User::select($columns)->with('roles:id,name,role_key')->where('status', 0)->whereNotNull('email_verified_at');

			if ($search = $request->input('search.value')) {
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%")
						->orWhere('user_uid', 'like', "%{$search}%")
						->orWhereHas('roles', function ($q) use ($search) {
							$q->where('name', 'like', "%{$search}%");
						});
				});
			}

			$recordsTotal = User::where('status', 0)->whereNotNull('email_verified_at')->count();
			$recordsFiltered = $query->count();

			$users = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

			$sn = $start;
			$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();
			/** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Users\User> $users */
			$users->transform(function ($row) use (&$sn, $zones) {

				$row->DT_RowId = 'row_' . $row->id;
				$row->view_url = route('backend_single_user', $row->id); // clickable row
				$row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';

				$row->SN = ++$sn;

				$row->unique_ids = '<div>'
					. '<p class="text-xs">' . $row->user_uid . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->username ?? "N/A") . '</p>'
					. '</div>';

				// ------------- Avatar -------------
				$userAvatar = $row->avatar ? Storage::url($row->avatar) : asset('site_assets/dummies/dummy_man.webp');
				$row->avatar_img = '<img src="' . $userAvatar . '" class="w-12 h-12 rounded-lg mx-auto">';

				// ------------- User name -------------
				$row->name = '<div>'
					. '<p class="text-sm">' . $row->name . '</p>'
					. '</div>';

				// ------------- Email + Phone -------------
				$row->email = '<div>'
					. '<p class="text-sm">' . $row->email . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->phone ?? "N/A") . '</p>'
					. '</div>';

				// ------------- Position -------------
				$row->position_name = $row->position ? $row?->userRelatingBackTo_Instrument?->name : null;

				// ------------- Created date -------------
				$row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

				// ------------- Role badge -------------
				$row->role = '<div class="flex flex-col items-center justify-center gap-1 text-center"> 
					<p class="my-1 px-2 text-xs text-gray-500 dark:text-gray-200 border rounded text-center">' . $row->roles->pluck('name')->implode(', ') . '</p>
					<p class="text-sm">' . DatatableHelper::roleColor($row->roles->pluck('role_key')->first()) . '</p>
				</div>';

				$row->actions = DatatableHelper::datatableAction($row, true, 'backend_user_toggle', 'backend_user_delete');

				return $row;
			});

			return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $users);
		} catch (Throwable $e) {
			return DatatableHelper::handleDatatableError($request, $e, true, "All users datatable error");
		}
	}
	// --------------- BLACKLISTS ---------------


	// --------------- PENDING ---------------

	public function pending_users_dataTable(Request $request)
	{
		try {
			$columns = ['id', 'user_uid', 'created_by', 'username', 'avatar', 'name', 'user_role', 'email', 'phone', 'guardian', 'position', 'created_at', 'status'];

			$draw = (int)$request->input('draw');
			$start = (int)$request->input('start', 0);
			$length = (int)$request->input('length', 25);
			$orderColIndex = (int)$request->input('order.0.column', 0);
			$orderCol = $columns[$orderColIndex] ?? 'id';
			$orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

			$query = User::select($columns)->with('roles:id,name,role_key')->where('status', 0)->whereNull('email_verified_at');

			if ($search = $request->input('search.value')) {
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%")
						->orWhere('user_uid', 'like', "%{$search}%")
						->orWhereHas('roles', function ($q) use ($search) {
							$q->where('name', 'like', "%{$search}%");
						});
				});
			}

			$recordsTotal = User::where('status', 0)->whereNull('email_verified_at')->count();
			$recordsFiltered = $query->count();

			$users = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

			$sn = $start;
			$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();
			
			/** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Users\User> $users */
			$users->transform(function ($row) use (&$sn, $zones) {

				$row->DT_RowId = 'row_' . $row->id;
				$row->view_url = route('backend_single_user', $row->id); // clickable row
				$row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';

				$row->SN = ++$sn;

				$row->unique_ids = '<div>'
					. '<p class="text-xs">' . $row->user_uid . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->username ?? "N/A") . '</p>'
					. '</div>';

				// ------------- Avatar -------------
				$userAvatar = $row->avatar ? Storage::url($row->avatar) : asset('site_assets/dummies/dummy_man.webp');
				$row->avatar_img = '<img src="' . $userAvatar . '" class="w-12 h-12 rounded-lg mx-auto">';

				// ------------- User name -------------
				$row->name = '<div>'
					. '<p class="text-sm">' . $row->name . '</p>'
					. '</div>';

				// ------------- Email + Phone -------------
				$row->email = '<div>'
					. '<p class="text-sm">' . $row->email . '</p>'
					. '<p class="mt-1 text-gray-500 text-xs">' . ($row->phone ?? "N/A") . '</p>'
					. '</div>';
					
				// ------------- Position -------------
				$row->position_name = $row->position ? $row?->userRelatingBackTo_Instrument?->name : null;

				// ------------- Created date -------------
				$row->created_at_formatted = $row->created_at ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

				// ------------- Role badge -------------
				$row->role = '<div class="flex flex-col items-center justify-center gap-1 text-center"> 
					<p class="my-1 px-2 text-xs text-gray-500 dark:text-gray-200 border rounded text-center">'. $row->roles->pluck('name')->implode(', ') . '</p>
					<p class="text-sm">'. DatatableHelper::roleColor($row->roles->pluck('role_key')->first()) . '</p>
				</div>';

				$row->actions = DatatableHelper::datatableAction($row, true, 'backend_user_toggle', 'backend_user_delete');

				return $row;
			});

			return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $users);
		} catch (Throwable $e) {
			return DatatableHelper::handleDatatableError($request, $e, true, "All users datatable error");
		}
	}
	// --------------- PENDING ---------------
}
