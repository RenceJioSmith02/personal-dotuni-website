<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('program_courses', function (Blueprint $table) {
            $table->unique(
                ['program_id', 'course_id', 'requirement_category_id'],
                'program_courses_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('program_courses', function (Blueprint $table) {
            $table->dropUnique('program_courses_unique');
        });
    }
};
