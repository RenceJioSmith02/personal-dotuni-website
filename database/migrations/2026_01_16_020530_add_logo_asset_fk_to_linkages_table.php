<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('linkages', function (Blueprint $table) {

            // If column does NOT exist yet
            if (!Schema::hasColumn('linkages', 'logo_asset_id')) {
                $table->foreignId('logo_asset_id')
                    ->nullable()
                    ->after('description')
                    ->constrained('assets')
                    ->nullOnDelete();
            } else {
                // If column exists but no FK
                $table->foreign('logo_asset_id')
                    ->references('id')
                    ->on('assets')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('linkages', function (Blueprint $table) {

            $table->dropForeign(['logo_asset_id']);

            // Only drop column if you want rollback to fully remove it
            // $table->dropColumn('logo_asset_id');
        });
    }
};
