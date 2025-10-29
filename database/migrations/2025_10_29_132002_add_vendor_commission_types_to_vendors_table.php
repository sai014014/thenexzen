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
        // Add the new commission_type options to vendors table
        DB::statement("ALTER TABLE vendors MODIFY COLUMN commission_type ENUM('fixed_amount', 'percentage_of_revenue', 'per_booking_per_day', 'lease_to_rent') NULL");
        
        // Add lease_commitment_months column
        Schema::table('vendors', function (Blueprint $table) {
            $table->tinyInteger('lease_commitment_months')->nullable()->after('commission_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('lease_commitment_months');
        });
        
        // Revert enum back to original
        DB::statement("ALTER TABLE vendors MODIFY COLUMN commission_type ENUM('fixed_amount', 'percentage_of_revenue') NULL");
    }
};
