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
		Schema::create('user_verifications', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('type'); // 'email_verification' or 'password_reset'
			$table->string('otp', 6);
			$table->string('new_value')->nullable();
			$table->integer('expiry_duration')->nullable()->comment('in minutes');
			$table->timestamp('expires_at');
			$table->timestamps();

			$table->index(['user_id', 'type']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('user_verifications');
	}
};
