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
		Schema::create('lesson_groups', function (Blueprint $table) {
			$table->id();
			$table->string('group_uid')->unique();

			$table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
			$table->foreignId('subject_id')->nullable()->constrained('instruments')->nullOnDelete();

			$table->string('title');
			$table->string('difficulty')->nullable()->comment('beginner, intermediate, advanced');

			$table->json('schedule_day')->nullable();
			$table->date('starting_date')->nullable();
			$table->time('start_time')->nullable();
			$table->time('end_time')->nullable();

			$table->text('remarks')->nullable();

			$table->foreignId('creator')->constrained('users')->cascadeOnDelete();
			$table->foreignId('last_modifier')->nullable()->constrained('users')->nullOnDelete();
			$table->boolean('status')->default(1);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('lesson_groups');
	}
};
