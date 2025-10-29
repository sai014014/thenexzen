<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the transmission_type enum to include 'hybrid'
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN transmission_type ENUM('manual', 'automatic', 'hybrid', 'gear', 'gearless') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum without 'hybrid'
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN transmission_type ENUM('manual', 'automatic', 'gear', 'gearless') NULL");
    }
};
