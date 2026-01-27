<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dotuni_news', function (Blueprint $table) {
            $table->longText('article_body')->nullable()->after('headline');
        });
    }

    public function down(): void
    {
        Schema::table('dotuni_news', function (Blueprint $table) {
            $table->dropColumn('article_body');
        });
    }
};
