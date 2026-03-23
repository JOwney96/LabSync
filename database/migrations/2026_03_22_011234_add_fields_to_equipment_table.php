<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('category')->nullable()->after('name');
            $table->string('serial_number')->nullable()->unique()->after('category');
            $table->string('status')->default('available')->after('serial_number');
            $table->integer('quantity')->default(1)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'category',
                'serial_number',
                'status',
                'quantity',
            ]);
        });
    }
};
