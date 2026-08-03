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
        Schema::create('writing_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('prompt_topic');
            $table->longText('user_response');
            $table->integer('word_count')->default(0);
            $table->longText('ai_corrected_version')->nullable();
            $table->longText('ai_explanation')->nullable();
            $table->integer('grammar_score')->nullable();
            $table->integer('vocabulary_score')->nullable();
            $table->integer('naturalness_score')->nullable();
            $table->integer('clarity_score')->nullable();
            $table->string('cefr_estimate')->nullable();
            $table->longText('rewrite_attempt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_sessions');
    }
};
