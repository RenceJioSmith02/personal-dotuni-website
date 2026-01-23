<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dotuni_news', function (Blueprint $table) {
            $table->id();

            $table->string('title', 250);
            $table->string('headline', 250)->nullable();
            $table->string('slug', 250)->unique();

            $table->string('seo_title', 250);
            $table->string('seo_description', 300);

            $table->enum('status', [
                'draft',
                'submitted',
                'published',
                'archived'
            ])->default('draft');

            $table->enum('visibility', [
                'public',
                'private',
                'unlisted'
            ])->default('public');

            $table->foreignId('author_id')
                ->constrained('users');

            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users');

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dotuni_news');
    }
};
