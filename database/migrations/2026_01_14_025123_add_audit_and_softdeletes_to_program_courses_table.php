<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('program_courses', function (Blueprint $table) {
            $table->timestamps(); // created_at, updated_at
            $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('program_courses', function (Blueprint $table) {
            $table->dropColumn([
                'created_at',
                'updated_at',
                'updated_by',
                'deleted_at',
            ]);
        });
    }
};
