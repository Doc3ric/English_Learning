<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_sessions', function (Blueprint $table) {
            $table->integer('time_taken_seconds')->nullable()->after('article_word_count');
            $table->integer('words_per_minute')->nullable()->after('time_taken_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('reading_sessions', function (Blueprint $table) {
            $table->dropColumn(['time_taken_seconds', 'words_per_minute']);
        });
    }
};
