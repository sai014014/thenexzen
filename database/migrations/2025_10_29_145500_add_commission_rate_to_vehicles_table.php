<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vehicles', 'commission_rate')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->decimal('commission_rate', 10, 2)->nullable()->after('commission_type');
            });

            // Backfill from legacy column if present
            if (Schema::hasColumn('vehicles', 'commission_value')) {
                DB::statement('UPDATE vehicles SET commission_rate = commission_value WHERE commission_value IS NOT NULL');
            }
        }

        // Ensure lease_commitment_months exists (idempotent)
        if (!Schema::hasColumn('vehicles', 'lease_commitment_months')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->tinyInteger('lease_commitment_months')->nullable()->after('commission_rate');
            });
        }
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'commission_rate')) {
                $table->dropColumn('commission_rate');
            }
            if (Schema::hasColumn('vehicles', 'lease_commitment_months')) {
                $table->dropColumn('lease_commitment_months');
            }
        });
    }
};


