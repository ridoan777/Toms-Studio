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
		Schema::create('lesson_note_galleries', function (Blueprint $table) {
			$table->id();

			$table->foreignId('note_id')->constrained('lesson_notes')->cascadeOnDelete();

			$table->string('filename')->nullable();
			$table->string('filepath');
			$table->json('metadata')->nullable();

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
		Schema::dropIfExists('lesson_note_galleries');
	}
};
