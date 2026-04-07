<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Adds columns required by the Equipment model that were defined in the
     * 2026_03_24 migration, which could not run because the equipment table
     * already existed from the earlier 2026_03_22 migration.
     */
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('tag_id')->unique()->after('name');
            $table->string('location')->nullable()->after('category');
            $table->date('calibration_due')->nullable()->after('location');
            $table->date('purchase_date')->nullable()->after('calibration_due');
            $table->text('notes')->nullable()->after('purchase_date');
            $table->softDeletes(); // required by Equipment::SoftDeletes
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropUnique(['tag_id']);
            $table->dropColumn([
                'tag_id',
                'location',
                'calibration_due',
                'purchase_date',
                'notes',
                'deleted_at',
            ]);
        });
    }
};
