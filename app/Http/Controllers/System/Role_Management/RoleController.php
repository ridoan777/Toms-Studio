<?php
namespace App\Http\Controllers\System\Role_Management;

use App\Helpers\Errors\ExceptionHandling;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\Users\User;
use Spatie\Permission\Models\{Role, Permission};
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{RoleMiddleware, PermissionMiddleware};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller implements HasMiddleware
{
	use AuthorizesRequests;

	public static function middleware(): array
	{
		return [
			new Middleware(PermissionMiddleware::using('role_index'), only: ['index', 'create']),
			new Middleware(PermissionMiddleware::using('role_show'), only: ['show']),
			new Middleware(PermissionMiddleware::using('role_create'), only: ['store']),
			new Middleware(PermissionMiddleware::using('role_update'), only: ['update', 'toggle']),
			new Middleware(PermissionMiddleware::using('role_delete'), only: ['delete']),
		];
	}

	public function index()
	{
		$roles = Role::with('permissions')->get();
		return view("Admin.sidebar.Roles.index", compact('roles'));
	}

	public function create()
	{
		$permissions = Permission::all();
		return view("Admin.sidebar.Roles.create", compact('permissions'));
	}

	public function store(Request $request)
	{
		// $this->authorize('admin_create');
		$validated = $request->validate([
			'name' => 'required|min:4|max:32',
			'permission_list' => 'required|array',
			'permission_list.*' => 'string|exists:permissions,name|max:32|min:4'
		]);
		try {
			$roleName = strtolower($validated['name']);
			$roleKey = Str::of($validated['name'])->lower()->trim()
				->replaceMatches('/[^a-z0-9\s]/', '')
				->replaceMatches('/\s+/', '_')
				->trim('_');

			$role = Role::create([
				'name' => $roleName,
				'role_key' => (string)$roleKey,
				'role_type' => "custom",
			]);

			$role->syncPermissions(array_values($request->permission_list));

			return redirect()->back()->with("success", "Role '{$validated['name']}' has been created.");
		} catch (RoleAlreadyExists $e) {
			return redirect()->back()->with("error", "Role '{$validated['name']}' already exists!");
		} catch (Exception $e) {
			return redirect()->back()->with("error", "Role creation error:!" . $e->getMessage());
		}
	}

	public function show(Role $role)
	{
		$permissions = Permission::all();
		return view('Admin.sidebar.Roles.show', compact('role', 'permissions'));
	}

	public function update(Request $request, Role $role)
	{
		try {
			$USER = $request->user();
			if ((strtolower($role->role_type) === 'core') && !$USER?->can('role_core_roles')) {
				ExceptionHandling::bailout(403, "You don't have permissions to modify core roles!");
			}

			$validated = $request->validate([
				'name' => 'required|min:4|unique:roles,name,' . $role->id,
				'permission_list' => 'required|array',
				'permission_list.*' => 'string|exists:permissions,name'
			]);

			$role->update([
				'name' => $validated['name'],
			]);

			$message = "Role '{$validated['name']}' has been updated.";
			// -------- SYNC PERMISSIONS --------
			if($role->role_key !== 'super_admin'){
				$role->syncPermissions($validated['permission_list']);
			} else{
				$message = "Changes are saved! However, you cannot change super admin's permissions. Try using Admin roles.";
			}
			app(PermissionRegistrar::class)->forgetCachedPermissions();

			return redirect()->back()->with("success", $message);

		} catch (Exception $e) {
			return redirect()->back()->with("error", "Role updating error: " . $e->getMessage());
		}
	}

	public function delete(Request $request, Role $role)
	{
		try {
			$name = $role?->name ?? null;
			$USER = $request->user();
			if ((strtolower($role->role_type) === 'core') && !$USER?->can('role_core_roles')) {
				ExceptionHandling::bailout(403, "You don't have permissions to delete core roles!");
			}

			User::role($role->name)->chunk(100, function ($users) {
				foreach ($users as $user) {
					$user->syncRoles(['guest']);
				}
			});

			$role->delete();

			return redirect()->back()->with("success", "Role '{$name}' has been deleted successfully! Users were auto assigned to 'guest' role.");
		} catch (Exception $e) {
			return redirect()->back()->with("error", "Role deletion error!" . $e->getMessage());
		}
	}
}