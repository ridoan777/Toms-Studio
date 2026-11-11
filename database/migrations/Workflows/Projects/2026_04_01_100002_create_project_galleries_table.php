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
		Schema::create('project_galleries', function (Blueprint $table) {
			$table->id();
			$table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

			$table->string('filename')->nullable();
			$table->string('file');

			$table->json('metadata')->nullable();

			$table->boolean('status')->default(1);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('project_galleries');
	}
};
