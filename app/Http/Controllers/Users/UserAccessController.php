<?php
# this controller will only contain 1st parent (or, visible contents) of admin dashboard. For nested or children, they should have separate controllers and route files

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Helpers\{FileHelpers\FileManagement, IconPack, UidGenerator};
use App\Http\Requests\Profiles\WebProfileUpdateRequest;
use App\Imports\UsersImport;
use App\Models\System\Settings\UserAppPreference;
use App\Models\Users\{User, UserVerification};
use Illuminate\Support\Facades\{Auth, Hash, Log, Storage};
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Spatie\Permission\Models\Role;
use Throwable;

class UserAccessController extends Controller
{
	public function store(User $user)
	{
		try {
			if (strtolower($user->user_role) === 'admin' && strtolower($user->email) === 'alpha@test.com') {
				return redirect()->back()->with('error', '403 Aborting! Attempt to delete a master admin.');
			}
			$name = $user->name;

			$DISK_FOLDER = config('filesystems.default');
			FileManagement::deleteFile($user->avatar, $DISK_FOLDER);
			$user->delete();

			return redirect()->back()->with('success', "User '{$name}' deleted successfully!");
		} catch (Exception $e) {
			return redirect()->back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
		}
	}
}
