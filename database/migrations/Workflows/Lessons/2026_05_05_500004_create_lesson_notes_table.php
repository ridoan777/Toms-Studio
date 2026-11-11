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
		Schema::create('lesson_notes', function (Blueprint $table) {
			$table->id();
			$table->string('note_uid')->unique();

			$table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();
			$table->foreignId('group_id')->nullable()->constrained('lesson_groups')->cascadeOnDelete();
			$table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();

			$table->string('title');
			$table->text('note')->nullable();

			$table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
			$table->foreignId('last_modifier_id')->nullable()->constrained('users')->nullOnUpdate();
			$table->boolean('status')->default(1);

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('lesson_notes');
	}
};
