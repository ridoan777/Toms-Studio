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
		Schema::create('dispute_reports', function (Blueprint $table) {
			$table->id();
			$table->string('ticket', 255)->unique();
			
			$table->morphs('dispute_report');
			
			$table->string('issue_section', 255)->nullable();
			$table->foreignId('victim_id')->constrained('users')->cascadeOnDelete();
			$table->foreignId('accused_id')->constrained('users')->cascadeOnDelete();
			
			$table->integer('original_id')->nullable();
			$table->text('original_content')->nullable();

			$table->string('issue_label', 255);
			$table->text('description')->nullable();

			$table->text('message_victim')->nullable();
			$table->text('message_accused')->nullable();
			
			$table->boolean('is_resolved')->default(0);
			$table->boolean('status')->default(1);

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('dispute_reports');
	}
};
