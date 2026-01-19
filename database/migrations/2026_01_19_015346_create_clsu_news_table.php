<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clsu_news', function (Blueprint $table) {
            $table->id();

            $table->string('title', 150);
            $table->string('url', 1000)->nullable();
            $table->string('description', 500)->nullable();

            // FK to assets (thumbnail)
            $table->unsignedBigInteger('thumbnail_asset_id')->nullable();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('thumbnail_asset_id')
                ->references('id')
                ->on('assets')
                ->nullOnDelete();

            // Optional user relation
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            // Performance indexes
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clsu_news');
    }
};
