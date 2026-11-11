<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Helpers\{ApiJsonReturnHelper, Errors\ExceptionHandling};
use App\Models\Users\{User};
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Throwable;

class ApiUserController extends Controller implements HasMiddleware
{
	use AuthorizesRequests;
	public static function middleware(): array
	{
		return [
			new Middleware(PermissionMiddleware::using('user_index', 'indexByRolekey'), only: ['index']),
			new Middleware(PermissionMiddleware::using('user_show'), only: ['show']),
		];
	}
	// -----------------------------------------------------

	public function index()
	{
		try {
			$allUsers = User::select(['id', 'user_uid', 'user_role', 'name', 'email', 'city', 'avatar', 'timezone', 'status'])->paginate(20);

			return ApiJsonReturnHelper::handle(true, 200, "All users have been fetched successfully!", $allUsers);
		} catch (Throwable $e) {
			return ExceptionHandling::handle("fetching all user data!", $e);
		}
	}
	// -----------------------------------------------------

	public function indexByRolekey($roleKey = null)
	{
		try {
			$allUsers = User::filterRoleKey($roleKey)->select(['id', 'user_uid', 'user_role', 'name', 'email', 'city', 'avatar', 'timezone', 'status'])->paginate(20);

			return ApiJsonReturnHelper::handle(true, 200, "All users with '{$roleKey}' role have been fetched successfully!", $allUsers);
		} catch (Throwable $e) {
			return ExceptionHandling::handle("fetching '{$roleKey}'s!", $e);
		}
	}
	// -----------------------------------------------------

	public function show(Request $request, User $user)
	{
		try {
			$USER = $request->user();

			$getUser = $user->only('id', 'user_uid', 'user_role', 'name', 'email', 'city', 'country', 'about', 'avatar', 'timezone', 'status', 'avatar_url');

			if ($USER && strtolower($USER->user_role) === 'landlord') {
				$getUser = $user->only('id', 'user_uid', 'user_role', 'name', 'email', 'phone', 'dob', 'street', 'city', 'zip', 'country', 'about', 'agency', 'skills', 'timezone', 'status', 'avatar', 'avatar_url', 'nid_image', 'nid_image_url', 'rent_image', 'rent_image_url', 'address_image', 'address_image_url');
			}

			return ApiJsonReturnHelper::handle(true, 200, 'User data fetched successfully!', $getUser);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('fetching user data!', $e);
		}
	}
	// ----------------------------------------------------
}
