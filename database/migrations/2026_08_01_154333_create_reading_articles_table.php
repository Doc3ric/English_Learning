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
        Schema::create('reading_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('level'); // Beginner / Intermediate / Advanced
            $table->float('target_band')->nullable();
            $table->longText('full_text');
            $table->string('source_url')->nullable();
            $table->integer('recommended_time_minutes')->default(15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_articles');
    }
};
