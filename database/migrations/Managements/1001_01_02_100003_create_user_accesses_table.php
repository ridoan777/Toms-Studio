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
		Schema::create('user_accesses', function (Blueprint $table) {
			$table->id();

			$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

			$table->string('email');
			$table->string('user_role');

			$table->boolean('join_ban')->default(0);
			$table->boolean('chat_ban')->default(0);
			$table->boolean('gallery_ban')->default(0);
			$table->boolean('store_ban')->default(0);
			$table->boolean('view_ban')->default(0);
			$table->boolean('account_hold')->default(0);

			$table->text('admin_note')->nullable();

			$table->dateTime('ban_expiry')->nullable();

			$table->boolean('status')->default(1);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('user_accesses');
	}
};
