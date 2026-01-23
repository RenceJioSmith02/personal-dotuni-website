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
            $table->string('slug', 250)->unique();

            // long article body
            $table->longText('content')->nullable();

            $table->string('seo_title', 250)->nullable();
            $table->string('seo_description', 300)->nullable();

            // optional thumbnail (asset)
            $table->foreignId('thumbnail_asset_id')
                ->nullable()
                ->constrained('assets')
                ->nullOnDelete();

            // status + visibility
            $table->string('status', 20)->default('draft');      // draft|submitted|published|archived
            $table->string('visibility', 20)->default('public'); // public|private|unlisted

            // author and audit
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // helpful indexes
            $table->index(['status', 'published_at']);
            $table->index(['visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dotuni_news');
    }
};
