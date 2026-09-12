<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Encrypted values (via Laravel's 'encrypted' cast) are noticeably
     * longer than the plaintext they replace, since they include an IV and
     * authentication tag. The original 'string' column would become a
     * size-limited VARCHAR(255) on databases like MySQL, which could
     * truncate a longer encrypted value. 'text' has no such limit.
     */
    public function up(): void
    {
        Schema::table('phishing_logs', function (Blueprint $table) {
            $table->text('password')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phishing_logs', function (Blueprint $table) {
            $table->string('password')->change();
        });
    }
};
