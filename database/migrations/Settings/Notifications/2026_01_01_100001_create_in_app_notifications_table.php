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
		Schema::create('in_app_notifications', function (Blueprint $table) {
			$table->id();

			// $table->unsignedBigInteger('user_id');
			$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

			$table->string('trigger_place')->nullable();
			$table->string('type')->nullable()->comment('login, info, order, system, message, payment');
			$table->string('severity')->nullable()->comment('normal, alert, warning, critical');

			$table->string('title')->nullable();
			$table->text('message')->nullable();

			$table->string('focus_name')->nullable();
			$table->string('focus_image')->nullable();

			$table->text('action_url')->nullable();
			$table->string('action_label')->nullable();

			$table->boolean('status')->default(1);

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('in_app_notifications');
	}
};
