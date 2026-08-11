<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 18 schema additions:
     * 1. Add `reason` column to `daily_plan_items` (for "Why this?" explanations)
     * 2. Create `daily_reflections` table
     */
    public function up(): void
    {
        // Step 2: Add reason to daily_plan_items
        Schema::table('daily_plan_items', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('description');
        });

        // Step 4: Daily Reflection table
        Schema::create('daily_reflections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(1);
            $table->date('date');
            // Checkboxes: booleans, each represents a self-reported activity
            $table->boolean('did_grammar')->default(false);
            $table->boolean('did_vocabulary')->default(false);
            $table->boolean('did_speaking')->default(false);
            $table->boolean('did_writing')->default(false);
            // Free text fields
            $table->text('what_was_difficult')->nullable();
            $table->string('new_expression')->nullable();
            $table->timestamps();

            // One reflection per user per day
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_plan_items', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
        Schema::dropIfExists('daily_reflections');
    }
};
