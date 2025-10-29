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
        // First, map legacy commission_type values to new format
        DB::statement("UPDATE vehicles SET commission_type = 'fixed' WHERE commission_type = 'fixed_amount'");
        DB::statement("UPDATE vehicles SET commission_type = 'percentage' WHERE commission_type = 'percentage_of_revenue'");
        
        // Add the new commission_type options
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN commission_type ENUM('fixed', 'percentage', 'per_booking_per_day', 'lease_to_rent') NULL");
        
        // Add lease_commitment_months column
        Schema::table('vehicles', function (Blueprint $table) {
            $table->tinyInteger('lease_commitment_months')->nullable()->after('commission_type');
        });
        
        // Add index for better query performance
        Schema::table('vehicles', function (Blueprint $table) {
            $table->index(['ownership_type', 'commission_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['ownership_type', 'commission_type']);
            $table->dropColumn('lease_commitment_months');
        });
        
        // Revert enum back to original
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN commission_type ENUM('fixed', 'percentage') NULL");
    }
};
