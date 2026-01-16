<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('linkages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('linkage_categories')
                ->cascadeOnDelete();

            $table->string('title', 150);
            $table->string('url', 1000);
            $table->string('description', 500)->nullable();

            $table->unsignedBigInteger('logo_asset_id')->nullable();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();

            // Optional FK (recommended)
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('linkages');
    }
};
