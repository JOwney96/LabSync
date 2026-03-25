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
        Schema::create('checkout_requests', function (Blueprint $table) {
            $table->id();

            // Foreign Keys linking the User and the Equipment
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');

            // The Request Details
            $table->date('start_date');
            $table->date('end_date');
            $table->text('purpose')->nullable(); // Why do they need it?

            // State Management
            $table->enum('status', [
                'pending',   // Waiting for admin approval
                'approved',  // Admin said yes, waiting for pickup
                'active',    // Currently in the user's possession
                'denied',    // Admin said no
                'returned',  // User gave it back
                'overdue'    // Past the end_date and not returned
            ])->default('pending');

            $table->text('admin_notes')->nullable(); // E.g., Reason for denial

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_requests');
    }
};
