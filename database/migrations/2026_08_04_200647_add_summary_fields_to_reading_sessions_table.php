<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_sessions', function (Blueprint $table) {
            $table->text('summary_response')->nullable()->after('article_word_count');
            $table->integer('summary_score')->nullable()->after('summary_response');
            $table->text('summary_feedback')->nullable()->after('summary_score');
            $table->text('missing_ideas')->nullable()->after('summary_feedback');       // JSON array
            $table->text('vocabulary_suggestions')->nullable()->after('missing_ideas'); // JSON array
        });
    }

    public function down(): void
    {
        Schema::table('reading_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'summary_response',
                'summary_score',
                'summary_feedback',
                'missing_ideas',
                'vocabulary_suggestions',
            ]);
        });
    }
};
