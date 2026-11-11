<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('option_envs', function (Blueprint $table) {
            $table->id();
            
            $table->string('var_type')->nullable();
            $table->string('name')->nullable();
            $table->boolean('needEncrypt')->default(0);
            $table->text('value')->nullable();

            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_envs');
    }
};
