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
        Schema::create('vocabularies', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->text('meaning');
            $table->string('pronunciation')->nullable();
            $table->string('part_of_speech')->nullable();
            $table->text('example_sentence')->nullable();
            $table->string('synonyms')->nullable();
            $table->string('antonyms')->nullable();
            $table->text('personal_note')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_mastered')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabularies');
    }
};
