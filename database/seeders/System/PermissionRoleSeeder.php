<?php

namespace Database\Seeders\System;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use Spatie\Permission\PermissionRegistrar;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionMap = config('app_core_permissions');
        $rolesConfig = config('app_core_roles');

        $allPermissions = [];

        // ---------------- CREATE PERMISSIONS ----------------
        foreach ($permissionMap as $group => $perms) {
            foreach ($perms as $perm) {
                $permissionName = "{$group}_{$perm['permission_name']}";
                $allPermissions[] = $permissionName;

                Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    ['group' => $group, 'note' => $perm['note'] ?? null]
                );
            }
        }

        // ---------------- HELPER: MAP PERMISSIONS FOR ROLES ----------------
        $mapPermissions = function(string $group, array $groupPerms, array $selectedNames): array {
            $mapped = [];
            foreach ($groupPerms as $perm) {
                if (in_array($perm['permission_name'], $selectedNames)) {
                    $mapped[] = $group . '_' . $perm['permission_name'];
                }
            }
            return $mapped;
        };

        // ---------------- ROLE PERMISSION MAP ----------------
        $rolePermissions = [
            $rolesConfig['SUPER_ADMIN'] => $allPermissions,

            $rolesConfig['ADMIN'] => array_merge(
                $mapPermissions('role', $permissionMap['role'], ['index','show','create','update','delete']),
                $mapPermissions('admin', $permissionMap['admin'], ['email']),
                $mapPermissions('general', $permissionMap['general'], ['index','show','create','update','delete']),
                $mapPermissions('project', $permissionMap['project'], ['index','show','create','update','delete']),
                $mapPermissions('user', $permissionMap['user'], ['index','show','create','update','delete', 'admin_view']),
                $mapPermissions('profile', $permissionMap['profile'], ['index','show','create','update','delete']),
                $mapPermissions('subscription', $permissionMap['subscription'], ['index','show','create','delete','cancel']),
                $mapPermissions('payment', $permissionMap['payment'], ['index','show','create','delete','cancel']),
                $mapPermissions('lesson', $permissionMap['lesson'], ['index','show','create','update','delete', 'cancel', 'schedule']),
                $mapPermissions('notes', $permissionMap['notes'], ['index','show','create','update','delete', 'file_edit', 'file_view']),
            ),

            $rolesConfig['TEACHER'] => array_merge(
                $mapPermissions('general', $permissionMap['general'], ['index','show','create','update','delete']),
                $mapPermissions('project', $permissionMap['project'], ['index','show']),
                $mapPermissions('user', $permissionMap['user'], ['index','show','create','update','delete', 'admin_view']),
                $mapPermissions('profile', $permissionMap['profile'], ['index','show','create','update','delete']),
                $mapPermissions('subscription', $permissionMap['payment'], ['index','show','create','delete','cancel']),
                $mapPermissions('payment', $permissionMap['payment'], ['index','show','create','delete','cancel']),
                $mapPermissions('lesson', $permissionMap['lesson'], ['index','show','create','update','delete']),
                $mapPermissions('notes', $permissionMap['notes'], ['index','show','create','update','delete', 'file_edit', 'file_view']),
            ),

            $rolesConfig['STUDENT'] => array_merge(
                $mapPermissions('general', $permissionMap['general'], ['index','show','create','update','delete']),
                $mapPermissions('project', $permissionMap['project'], ['index','show']),
                $mapPermissions('user', $permissionMap['user'], ['index','show','create','update','delete', 'admin_view']),
                $mapPermissions('profile', $permissionMap['profile'], ['index','show','create','update','delete']),
                $mapPermissions('subscription', $permissionMap['payment'], ['index','show','create','delete','cancel']),
                $mapPermissions('payment', $permissionMap['payment'], ['index','show','create','delete','cancel']),
                $mapPermissions('lesson', $permissionMap['lesson'], ['index','show','create','update','delete', 'cancel', 'schedule']),
                $mapPermissions('notes', $permissionMap['notes'], ['index','show','create','update','delete', 'file_edit', 'file_view']),
            ),

            $rolesConfig['GUEST'] => array_merge(
                $mapPermissions('general', $permissionMap['general'], ['index','show','create','update','delete']),
                $mapPermissions('profile', $permissionMap['profile'], ['index','show','create','update','delete']),
            ),
        ];

        // ---------------- CREATE ROLES + SYNC PERMISSIONS ----------------
        foreach ($rolePermissions as $roleName => $permissions) {
            $roleKey = array_search($roleName, $rolesConfig);

            $role = Role::firstOrCreate(
                ['role_key' => strtolower($roleKey)],
                [
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'role_type' => 'core'
                ]
            );

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}