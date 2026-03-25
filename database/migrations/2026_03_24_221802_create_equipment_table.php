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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();

            // Core Identification
            $table->string('name');
            $table->string('tag_id')->unique(); // e.g., LMC-092
            $table->string('category')->index(); // e.g., 'Microscope', 'Centrifuge'

            // State & Tracking
            $table->enum('status', [
                'available',
                'in_use',
                'maintenance',
                'retired'
            ])->default('available');

            $table->string('location')->nullable(); // e.g., 'Room A, Bench 3'

            // Lifecycle
            $table->date('calibration_due')->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('notes')->nullable();

            // Audit Trail
            $table->timestamps();
            $table->softDeletes(); // Important for labs: never truly delete equipment history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
