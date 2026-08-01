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
        Schema::create('reading_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reading_article_id')->constrained()->onDelete('cascade');
            $table->text('summary_text');
            $table->integer('word_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_summaries');
    }
};
