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
		Schema::create('option_email_templates', function (Blueprint $table) {
			$table->id();
			$table->string('flag');

			$table->string('subject');
			
			$table->text('greeting')->nullable();

			$table->text('body_message')->nullable();
			$table->text('end_message')->nullable();
			
			$table->text('support_message')->nullable();
			$table->text('support_details')->nullable();

			$table->boolean('status')->default(1);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('option_email_templates');
	}
};
