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
		Schema::create('payment_items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('payment_trx_id')->constrained('payment_transactions')->cascadeOnDelete();
			$table->string('payment_item_uid');
			$table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
			$table->foreignId('group_id')->nullable()->constrained('lesson_groups')->nullOnDelete();
			$table->foreignId('payer_id')->nullable()->constrained('users')->nullOnDelete()->comment('payer user_id');
			$table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete()->comment('student user_id');

			$table->string('phase')->default('created');

			$table->string('ss_payer_name')->nullable()->comment('snapshot');
			$table->string('ss_payer_email')->nullable()->comment('snapshot');
			$table->string('ss_student_name')->nullable()->comment('snapshot');
			$table->string('ss_student_email')->nullable()->comment('snapshot');
			$table->string('ss_currency')->nullable()->comment('snapshot');

			$table->integer('ss_amount')->default(0)->comment('snapshot amount in cents');
			$table->integer('stripe_fee_share')->default(0)->comment('divided fees on each item');

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
		Schema::dropIfExists('payment_items');
	}
};
