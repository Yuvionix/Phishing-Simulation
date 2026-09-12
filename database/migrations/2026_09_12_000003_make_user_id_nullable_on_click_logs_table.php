<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original click_logs table required user_id to be present, which
     * assumed only logged-in users would click a tracking link. In practice,
     * the people clicking a phishing simulation link are external targets,
     * not authenticated app users - so this column needs to be nullable.
     */
    public function up(): void
    {
        Schema::table('click_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('click_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
