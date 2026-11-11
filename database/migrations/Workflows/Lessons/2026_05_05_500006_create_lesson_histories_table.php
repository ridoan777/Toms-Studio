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
		Schema::create('lesson_histories', function (Blueprint $table) {
			$table->id();
			$table->string('lesson_history_uid')->unique();

			$table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();
			$table->foreignId('group_id')->nullable()->constrained('lesson_groups')->cascadeOnDelete();
			
			$table->string('phase')->comment('upcoming, in_progress, scheduled, completed, no_show, cancelled');

			$table->date('original_date')->nullable()->comment('only for scheduled');
			$table->time('original_start_time')->nullable()->comment('as per routine');
			$table->time('original_end_time')->nullable()->comment('as per routine');
			
			$table->date('final_date')->nullable()->comment('as per phase update');
			$table->time('final_start_time')->nullable()->comment('as per phase update');
			$table->time('final_end_time')->nullable()->comment('as per phase update');
			
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
		Schema::dropIfExists('lesson_histories');
	}
};
