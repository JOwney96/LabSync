<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('checkout_requests', function (Blueprint $table) {
            // We add the column after 'status' to keep the table logical.
            // Using nullable() ensures old test data doesn't break.
            $table->timestamp('request_date')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkout_requests', function (Blueprint $table) {
            // Always include this so you can 'migrate:rollback' if needed!
            $table->dropColumn('request_date');
        });
    }
};
