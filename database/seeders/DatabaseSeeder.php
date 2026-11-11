<?php

namespace Database\Seeders;

use Database\Seeders\System\{PermissionRoleSeeder, PermissionSeeder, SaticSiteSeeder};
use Database\Seeders\Workflows\{InstrumentSeeder, LessonSeeder, LessonGroupSeeder, LessonHistorySeeder};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		// User::factory(10)->create();

		/* --------------- SYSTEM ---------------  */
		//  $this->call(SaticSiteSeeder::class);

		/* --------------- REGULAR ---------------  */
		// $this->call(PermissionRoleSeeder::class);
		// $this->call(InstrumentSeeder::class);
		// $this->call(UserSeeder::class);
		// $this->call(BulkUserSeeder::class);
		// $this->call(LessonSeeder::class);
		$this->call(LessonGroupSeeder::class);
		// $this->call(LessonHistorySeeder::class);
		// $this->call(SubscribedUserSeeder::class);
		// $this->call(PermissionSeeder::class);
		//  $this->call(MailerSeeder::class);

	}
}
