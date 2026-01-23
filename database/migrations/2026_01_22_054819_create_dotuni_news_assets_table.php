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
                ->constrained('assets')
                ->cascadeOnDelete();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_cover')->default(false);

            // Prevent duplicate attachments for the same news
            $table->unique(['news_id', 'asset_id']);

            // Useful for ordering and cover lookup
            $table->index(['news_id', 'sort_order']);
            $table->index(['news_id', 'is_cover']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dotuni_news_assets');
    }
};
