<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vocabularies', function (Blueprint $table) {
            $table->string('source')->nullable()->after('is_mastered');
        });
    }

    public function down(): void
    {
        Schema::table('vocabularies', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
