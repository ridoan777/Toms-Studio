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
        Schema::create('chat_blocklists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('victim_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();

            $table->string('composite_a')->unique();
            $table->string('composite_b')->unique();
            
            $table->text('reason')->nullable();
            
            $table->boolean('is_blocked')->default(1);
            $table->boolean('status')->default(1);

            $table->timestamps();

            $table->unique(['victim_id', 'blocked_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_blocklists');
    }
};
