<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('program_requirements', function (Blueprint $table) {
            $table->unique(
                ['program_id', 'requirement_category_id'],
                'program_requirements_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('program_requirements', function (Blueprint $table) {
            $table->dropUnique('program_requirements_unique');
        });
    }
};
