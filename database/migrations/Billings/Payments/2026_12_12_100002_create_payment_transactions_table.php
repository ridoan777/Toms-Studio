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
		Schema::create('payment_transactions', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
			$table->string('trx_uid')->unique();
			
			$table->integer('ss_user_id')->nullable()->comment('snapshot');
			$table->string('ss_user_email')->nullable()->comment('snapshot');

			$table->string('payment_status')->nullable();
			$table->string('trx_phase')->nullable();

			$table->string('currency')->nullable();
			$table->integer('paid_amount')->default(0)->comment('stored in cents');
			$table->integer('stripe_fee')->default(0)->comment('stored in cents');
			$table->decimal('exchange_rate', 12,6)->default(0)->comment('useful for stripe fee count');
			$table->integer('takeaway_amount')->default(0)->comment('remaining in cents');
			
			$table->json('card_details')->nullable()->comment('array');
			$table->json('customer_details')->nullable()->comment('array');

			$table->text('payment_intent')->nullable();
			$table->json('event_ids')->nullable()->comment('all events');
			$table->text('stripe_checkout_session_id')->nullable();

			$table->text('note')->nullable();

			$table->boolean('status')->default(1);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('payment_transactions');
	}
};
