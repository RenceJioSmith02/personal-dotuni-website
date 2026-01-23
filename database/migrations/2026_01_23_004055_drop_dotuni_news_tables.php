<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('dotuni_news_assets');
        Schema::dropIfExists('dotuni_news');
    }

    public function down(): void
    {
        // intentionally left empty
    }
};
