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
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasColumn('notifications', 'business_id')) {
                    // Make business_id nullable since Laravel notifications store it in JSON data
                    $table->foreignId('business_id')->nullable()->change();
                } else {
                    // Add business_id as nullable if it doesn't exist
                    $table->foreignId('business_id')->nullable()->after('notifiable_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep nullable to avoid breaking existing data
    }
};

