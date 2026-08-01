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
        Schema::create('mistakes', function (Blueprint $table) {
            $table->id();
            $table->text('wrong_text');
            $table->text('correct_text');
            $table->text('reason')->nullable();
            $table->string('category'); // grammar, vocabulary, pronunciation
            $table->integer('times_reviewed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mistakes');
    }
};
