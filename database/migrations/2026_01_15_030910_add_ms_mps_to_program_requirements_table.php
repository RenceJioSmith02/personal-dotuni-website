<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('program_requirements', function (Blueprint $table) {
            $table->integer('ms')->nullable()->after('required_units');
            $table->integer('mps')->nullable()->after('ms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_requirements', function (Blueprint $table) {
            $table->dropColumn(['ms', 'mps']);
        });
    }
};
