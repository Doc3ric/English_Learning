<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('writing_sessions', function (Blueprint $table) {
            $table->longText('professional_version')->nullable()->after('rewrite_attempt');
            $table->longText('native_version')->nullable()->after('professional_version');
            // 12F: lightweight memory context injected at generation time (stored for debug)
            $table->text('memory_context')->nullable()->after('native_version');
        });
    }

    public function down(): void
    {
        Schema::table('writing_sessions', function (Blueprint $table) {
            $table->dropColumn(['professional_version', 'native_version', 'memory_context']);
        });
    }
};
