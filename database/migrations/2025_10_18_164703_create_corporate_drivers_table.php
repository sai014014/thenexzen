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
        Schema::create('corporate_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('driver_name');
            $table->string('driving_license_number');
            $table->date('license_expiry_date');
            $table->string('driving_license_path')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['customer_id']);
            $table->index(['driving_license_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_drivers');
    }
};