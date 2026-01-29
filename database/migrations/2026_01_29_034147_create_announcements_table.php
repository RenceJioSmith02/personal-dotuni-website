<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->string('title', 250);
            $table->longText('article_body')->nullable();

            $table->string('slug', 250)->unique();

            $table->string('seo_title', 250)->nullable();
            $table->string('seo_description', 300)->nullable();

            $table->enum('status', ['draft', 'submitted', 'published', 'archived'])
                ->default('draft');

            $table->enum('visibility', ['public', 'private', 'unlisted'])
                ->default('public');

            $table->string('layout', 50)->nullable();

            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->dateTime('publish_start')->nullable();
            $table->dateTime('publish_end')->nullable();

            $table->timestamps();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
