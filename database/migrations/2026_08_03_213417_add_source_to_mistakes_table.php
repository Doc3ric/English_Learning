<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mistakes', function (Blueprint $table) {
            $table->string('source')->nullable()->after('times_reviewed');
        });
    }

    public function down(): void
    {
        Schema::table('mistakes', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
