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
		Schema::create('payment_payables', function (Blueprint $table) {
			$table->id();
			$table->string('payable_uid')->unique()->comment('equal to payable_uid');
			
			$table->foreignId('student_id')->constrained('users');

			$table->nullableMorphs('payable');           // payable_type + payable_id → Lesson or LessonGroup

			$table->unsignedBigInteger('group_member_id')->nullable(); // GroupStudent.id for groups

			$table->date('week_start')->comment('due date|1st classday of the week'); // Monday (anchor key)
			$table->integer('amount')->comment('in cents');
			$table->tinyInteger('status')->default(0);   // 0=unpaid, 1=paid, 2=waived
			$table->timestamp('paid_at')->nullable();
			
			$table->tinyInteger('is_cancelled')->default(0);   // 0=unpaid, 1=paid, 2=waived
			$table->timestamp('cancel_req_at')->nullable();
			$table->text('cancel_reason')->nullable();
			$table->string('cancel_resolve', 255)->nullable()->comment('null|accept|reject');
			
			$table->unsignedBigInteger('payment_id')->nullable(); // Stripe payment ref later
			$table->unsignedBigInteger('created_by')->nullable();

			$table->timestamps();
			$table->timestamp('cancelled_at')->nullable();

			$table->unique(['student_id', 'payable_type', 'payable_id', 'week_start'], 'payables_student_type_id_week_unique'); // no duplicates
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('payment_payables');
	}
};
