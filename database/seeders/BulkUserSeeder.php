<?php

namespace Database\Seeders;

use App\Helpers\UidGenerator;
use App\Models\Users\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class BulkUserSeeder extends Seeder
{
	public function run(): void
	{
		$letters = range('A', 'Z');

		$names = ['Albert', 'Burners', 'Chapline', 'Dallas', 'Evans', 'Foster', 'Griffin', 'Harris', 'Irwin', 'Johnson', 'King', 'Lewis', 'Morris', 'Nelson', 'Owens', 'Parker', 'Quinn', 'Roberts', 'Stevens', 'Turner', 'Underwood', 'Vance', 'Walker', 'Xavier', 'Young', 'Zimmer'];

		$addresses = ['12 King St, Sydney NSW 2000', '45 Collins St, Melbourne VIC 3000', '78 Queen St, Brisbane QLD 4000', '21 Rundle Mall, Adelaide SA 5000', '9 Murray St, Perth WA 6000', '33 Northbourne Ave, Canberra ACT 2600', '56 Macquarie St, Hobart TAS 7000', '88 George St, Sydney NSW 2000', '14 Swanston St, Melbourne VIC 3000', '67 Adelaide St, Brisbane QLD 4000'];

		$avatars = ['user-avatars/haninabz-school.png', 'user-avatars/fountain_pen.jpg', 'user-avatars/rose.jpg', null];

		$teacherRole = Role::where('role_key', 'teacher')->first();
		$studentRole = Role::where('role_key', 'student')->first();

		$password = Hash::make('12345678');

		$users = [];
		$pivot = [];

		$startIndex = array_search('K', $letters);
		$totalUsers = 1000;

		for ($i = 0; $i < $totalUsers; $i++) {

			$letterIndex = ($startIndex + $i) % count($letters);
			$letter = $letters[$letterIndex];
			$name = ($names[$letterIndex] ?? 'User') . ' ' . $letter;

			$role = $i < 18 ? $teacherRole : $studentRole;

			$email = strtolower(str_replace(' ', '', $name)) . $i . '@test.com';

			$guardian = $role->role_key === 'student'
				? 'Gertrude ' . explode(' ', $name)[1] . ' (mother), Housewife'
				: null;

			$users[] = [
				'user_uid' => UidGenerator::uniqueULID($name, 8, 15, 4),
				'username' => UidGenerator::uniqueUsername($name, 8, 4),
				'name' => $name,
				'user_role' => $role->role_key,
				'created_by' => 1,
				'phone' => "+8801" . str_pad((string)$i, 8, '0', STR_PAD_LEFT),
				'password' => $password,
				'guardian' => $guardian,
				'position' => $role->role_key === 'teacher' ? rand(1, 15) : null,
				'address' => $addresses[array_rand($addresses)],
				'avatar' => $avatars[array_rand($avatars)],
				'remember_token' => Str::random(10),
				'email' => $email,
				'email_verified_at' => now(),
				'status' => 1,
				'created_at' => now(),
				'updated_at' => now(),
			];
		}

		// 🔥 Bulk insert users
		User::insert($users);

		// 🔥 Fetch inserted users once
		$insertedUsers = User::whereIn('email', array_column($users, 'email'))->get();

		foreach ($insertedUsers as $user) {
			$roleId = $user->user_role === 'teacher' ? $teacherRole->id : $studentRole->id;

			$pivot[] = [
				'role_id' => $roleId,
				'model_type' => User::class,
				'model_id' => $user->id
			];
		}

		// 🔥 Bulk insert roles
		DB::table('model_has_roles')->insert($pivot);
	}
}