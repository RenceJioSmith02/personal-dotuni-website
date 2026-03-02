<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('announcement_assets', function (Blueprint $table) {
            $table->text('caption')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('announcement_assets', function (Blueprint $table) {
            $table->string('caption', 500)->nullable()->change();
        });
    }

};