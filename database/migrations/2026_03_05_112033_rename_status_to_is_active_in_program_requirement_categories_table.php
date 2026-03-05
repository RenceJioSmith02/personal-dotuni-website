<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Optional: convert old string values to 1/0 first
        DB::table('program_requirement_categories')
            ->where('status', 'draft')
            ->update(['status' => 0]);

        DB::table('program_requirement_categories')
            ->where('status', 'published')
            ->update(['status' => 1]);

        Schema::table('program_requirement_categories', function (Blueprint $table) {
            // Change type to boolean
            $table->boolean('status')->default(1)->change();
        });

        Schema::table('program_requirement_categories', function (Blueprint $table) {
            // Rename column
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