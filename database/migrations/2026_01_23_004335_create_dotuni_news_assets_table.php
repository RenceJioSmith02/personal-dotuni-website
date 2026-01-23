<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dotuni_news_assets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('news_id')
                ->constrained('dotuni_news')
                ->cascadeOnDelete();

            $table->foreignId('asset_id')
                ->constrained('assets');

            $table->string('caption', 500)->nullable();

            $table->boolean('is_thumbnail')->default(false);
            $table->boolean('is_cover')->default(false);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dotuni_news_assets');
    }
};
