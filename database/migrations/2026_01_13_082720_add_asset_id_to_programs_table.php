<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('program_asset_id')
                ->nullable()
                ->constrained('assets')
                ->after('total_units');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['program_asset_id']); // ✅ correct column
            $table->dropColumn('program_asset_id');    // ✅ correct column
        });
    }
};
