<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_courses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requirement_category_id')->constrained('program_requirement_categories')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->integer('sort_order')->nullable();

            $table->unique(
                ['program_id', 'requirement_category_id', 'course_id'],
                'uq_program_courses'
            );
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('program_courses');
    }
};

