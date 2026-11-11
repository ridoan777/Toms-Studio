<?php

namespace Database\Seeders;

use App\Helpers\UidGenerator;
use App\Models\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
	public function run(): void
	{
		$users = [
			[ 'name' => 'Zeta Master', 'user_role' => 'SUPER_ADMIN', 'email' => 'admin@test.com'],
			[ 'name' => 'Yeti Joker', 'user_role' => 'GUEST', 'email' => 'joker@test.com'],

			[ 'name' => 'Alpha Albert', 'user_role' => 'ADMIN', 'email' => 'alpha@test.com'],
			[ 'name' => 'Beta Burners', 'user_role' => 'ADMIN', 'email' => 'beta@test.com'],

			[ 'name' => 'Charlie Chapline', 'user_role' => 'TEACHER', 'email' => 'charlie@test.com'],
			[ 'name' => 'Delta Dallas', 'user_role' => 'TEACHER', 'email' => 'delta@test.com'],
			[ 'name' => 'Echo Elijah', 'user_role' => 'TEACHER', 'email' => 'echo@test.com'],
			[ 'name' => 'Fenri Fox', 'user_role' => 'TEACHER', 'email' => 'fox@test.com'],

			[ 'name' => 'Gil Gamesh', 'user_role' => 'STUDENT', 'email' => 'gil@test.com'],
			[ 'name' => 'Home Lander', 'user_role' => 'STUDENT', 'email' => 'home@test.com'],
		];

		foreach ($users as $index => $item) {

			$roleKey = strtolower($item['user_role']);

			$role = Role::where('role_key', $roleKey)->first();

			if (!$role) continue;

			$user = User::updateOrCreate(
				['email' => $item['email']],
				[
					'user_uid' => UidGenerator::uniqueULID($item['name'], 8, 15, 4),
					'username' => UidGenerator::uniqueUsername($item['name'], 8, 4),
					'name' => $item['name'],
					'user_role' => $roleKey,	// must be role_key, not name
					'created_by' => (int)1,

					'phone' => "+{$index}-123-4567",
					'password' => Hash::make('12345678'),

					'guardian' => null,
					'position' => $roleKey === 'teacher' ? rand(1,5) : null,
					'address' => null,

					'remember_token' => Str::random(10),
					'email_verified_at' => now(),

					'status' => 1
				]
			);

			$user->syncRoles([$role]);
		}
	}
}