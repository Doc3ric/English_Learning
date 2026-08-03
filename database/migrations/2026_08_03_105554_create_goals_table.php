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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->integer('target_vocabulary')->default(70);
            $table->integer('target_grammar')->default(5);
            $table->integer('target_reading')->default(7);
            $table->integer('target_writing')->default(7);
            $table->integer('target_study_time')->default(480);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
