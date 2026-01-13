<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Add new columns
            $table->boolean('is_active')->default(true)->after('name');
            $table->foreignId('updated_by')
                  ->nullable()
                  ->after('updated_at')
                  ->constrained('users')
                  ->nullOnDelete();

            // Soft deletes
            $table->softDeletes();

            // Remove unused columns (optional but recommended)
            $table->dropColumn('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->renameColumn('display_name', 'name');
            $table->renameColumn('password_hash', 'password');

            $table->dropColumn('is_active');
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');

            $table->dropSoftDeletes();
            $table->timestamp('email_verified_at')->nullable();
        });
    }
};
