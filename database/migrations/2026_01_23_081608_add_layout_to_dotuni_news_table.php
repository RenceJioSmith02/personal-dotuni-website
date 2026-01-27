<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dotuni_news', function (Blueprint $table) {
            // Option A (simple string)
            $table->string('layout', 50)->default('layout_1')->after('visibility');

            // Option B (enum) - uncomment if you prefer strict values
            // $table->enum('layout', ['layout_1','layout_2','layout_3'])->default('layout_1')->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('dotuni_news', function (Blueprint $table) {
            $table->dropColumn('layout');
        });
    }
};
