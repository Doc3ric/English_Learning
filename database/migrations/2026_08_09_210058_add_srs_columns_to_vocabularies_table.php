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
        Schema::table('vocabularies', function (Blueprint $table) {
            $table->integer('leitner_box')->default(1)->after('is_mastered');
            $table->date('next_review_date')->nullable()->after('leitner_box');
            $table->timestamp('last_reviewed_at')->nullable()->after('next_review_date');
        });

        // Data Migration
        // Existing mastered words -> Box 5, reviewed 30 days from today
        DB::table('vocabularies')->where('is_mastered', true)->update([
            'leitner_box' => 5,
            'next_review_date' => now()->addDays(30)->toDateString(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vocabularies', function (Blueprint $table) {
            $table->dropColumn(['leitner_box', 'next_review_date', 'last_reviewed_at']);
        });
    }
};
