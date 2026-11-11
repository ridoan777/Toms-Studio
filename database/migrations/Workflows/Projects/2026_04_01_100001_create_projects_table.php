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
		Schema::create('projects', function (Blueprint $table) {
			$table->id();
			$table->string('project_uid')->unique();
			$table->foreignId('project_manager_id')->constrained('users')->cascadeOnDelete();

			$table->string('title');

			$table->string('phase')->nullable();    // on-track, completed
			$table->string('progress')->nullable();
			$table->string('location')->nullable();
			$table->date('start_date')->nullable();
			$table->date('target_date')->nullable();

			$table->text('description')->nullable();
			$table->string('image')->nullable();
			$table->string('video')->nullable();
			$table->json('video_gallery')->nullable();

			$table->boolean('status')->default(1);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('projects');
	}
};
