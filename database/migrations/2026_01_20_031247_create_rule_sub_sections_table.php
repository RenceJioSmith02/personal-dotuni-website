<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rule_sub_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('rule_sections')->cascadeOnDelete();
            $table->string('number', 20);
            $table->text('body');
            $table->integer('sort_order')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_sub_sections');
    }
};
