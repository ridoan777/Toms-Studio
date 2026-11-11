<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('chat_group_members', function (Blueprint $table) {
			$table->id();

			$table->foreignId('chat_group_id')->constrained('chat_groups')->cascadeOnDelete();
			$table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
			$table->foreignId('adder_id')->nullable()->constrained('users')->nullOnDelete();
			
			$table->string('member_role', 255)->default('member');	// member, moderator, room_admin

			$table->text('username_in_room')->nullable();
			$table->text('avatar_in_room')->nullable();

			$table->boolean('status')->default(1);

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('chat_group_members');
	}
};
