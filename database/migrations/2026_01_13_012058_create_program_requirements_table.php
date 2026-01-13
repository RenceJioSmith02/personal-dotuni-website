<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requirement_category_id')
                ->constrained('program_requirement_categories')
                ->cascadeOnDelete();
            $table->integer('required_units')->nullable();
            $table->timestamps();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();

            $table->unique(['program_id', 'requirement_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_requirements');
    }
};


