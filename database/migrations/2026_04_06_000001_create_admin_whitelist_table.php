<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_whitelist', function (Blueprint $table) {
            $table->id();

            // The email address that is pre-approved for admin registration
            $table->string('email')->unique()->index();

            // Optional context for why this email was whitelisted
            $table->text('notes')->nullable();

            // Audit: which admin added this entry
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_whitelist');
    }
};
