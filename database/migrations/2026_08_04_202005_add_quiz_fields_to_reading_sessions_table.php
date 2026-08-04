<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_sessions', function (Blueprint $table) {
            $table->json('quiz_data')->nullable()->after('vocabulary_suggestions');
            $table->integer('quiz_score')->nullable()->after('quiz_data');
            $table->json('quiz_answers')->nullable()->after('quiz_score');
        });
    }

    public function down(): void
    {
        Schema::table('reading_sessions', function (Blueprint $table) {
            $table->dropColumn(['quiz_data', 'quiz_score', 'quiz_answers']);
        });
    }
};
