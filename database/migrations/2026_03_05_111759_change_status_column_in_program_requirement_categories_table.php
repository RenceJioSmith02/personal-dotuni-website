<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('program_requirement_categories', function (Blueprint $table) {
            $table->boolean('status')
                ->default(1)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('program_requirement_categories', function (Blueprint $table) {
            $table->string('status')
                ->default('draft')
                ->change();
        });
    }
};