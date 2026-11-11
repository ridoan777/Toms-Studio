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
		Schema::create('lesson_group_students', function (Blueprint $table) {
			$table->id();
			$table->string('member_uid')->unique();

			$table->foreignId('group_id')->constrained('lesson_groups')->cascadeOnDelete();
			$table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
			
			$table->integer('fees')->default(0)->comment('in cents');
			
			$table->foreignId('adder')->constrained('users')->comment('person who added 1st')->cascadeOnDelete();
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
		Schema::dropIfExists('lesson_group_students');
	}
};
