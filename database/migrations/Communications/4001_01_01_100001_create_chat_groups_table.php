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
		Schema::create('chat_groups', function (Blueprint $table) {
			$table->id();
			$table->string('chat_group_uid', 255)->unique();

			$table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
			
			$table->string('title', 255);
			$table->text('description')->nullable();
			$table->integer('total_members')->default(1);
			
			$table->text('image')->nullable();

			$table->boolean('status')->default(1);

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('chat_groups');
	}
};
