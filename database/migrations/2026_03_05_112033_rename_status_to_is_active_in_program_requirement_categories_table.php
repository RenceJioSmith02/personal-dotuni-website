<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

public function up(): void
{
    Schema::table('program_requirement_categories', function (Blueprint $table) {
        $table->renameColumn('status', 'is_active');
    });
}

    public function down(): void
    {
        Schema::table('program_requirement_categories', function (Blueprint $table) {
            $table->renameColumn('is_active', 'status');
        });

        Schema::table('program_requirement_categories', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }
};
