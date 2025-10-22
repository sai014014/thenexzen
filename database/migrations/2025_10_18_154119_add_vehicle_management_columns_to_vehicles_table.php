<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Business relationship
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            
            // Vehicle Type Selection (Mandatory)
            $table->enum('vehicle_type', ['car', 'bike_scooter', 'heavy_vehicle'])->default('car');
            
            // Common Vehicle Information (for all vehicle types)
            $table->string('vehicle_make'); // Manufacturer (e.g., Maruti, Tata, Honda)
            $table->string('vehicle_model'); // Specific model (e.g., Swift, Pulsar, XUV500)
            $table->year('vehicle_year'); // Manufacturing year
            $table->string('vehicle_number')->unique(); // License plate/registration number
            $table->enum('vehicle_status', ['active', 'inactive', 'under_maintenance'])->default('active');
            
            // Common Fields for All Vehicle Types
            $table->enum('fuel_type', ['petrol', 'diesel', 'cng', 'electric', 'hybrid']);
            $table->decimal('mileage', 8, 2)->nullable(); // Text Field to input numbers
            $table->enum('transmission_type', ['manual', 'automatic', 'gear', 'gearless'])->nullable();
            
            // Category-Specific Fields
            // For Cars
            $table->integer('seating_capacity')->nullable(); // For cars and buses
            
            // For Bikes and Scooters
            $table->integer('engine_capacity_cc')->nullable(); // Engine cubic capacity (e.g., 150cc, 250cc)
            
            // For Heavy Vehicles (DCMs, Trucks, Buses)
            $table->decimal('payload_capacity_tons', 8, 2)->nullable(); // Weight capacity in tons
            
            // Rental Pricing and Usage Information
            $table->decimal('rental_price_24h', 10, 2)->nullable(); // Base price for 24 hours
            $table->integer('km_limit_per_booking')->nullable(); // Base km limit per booking
            $table->decimal('extra_rental_price_per_hour', 8, 2)->nullable(); // Extra charge per hour
            $table->decimal('extra_price_per_km', 8, 2)->nullable(); // Extra charge per km after limit
            
            // Ownership and Vendor Details
            $table->enum('ownership_type', ['owned', 'leased', 'vendor_provided']);
            $table->string('vendor_name')->nullable(); // Optional for Vendor-Provided Vehicles
            $table->enum('commission_type', ['fixed', 'percentage'])->nullable(); // Commission type
            $table->decimal('commission_value', 8, 2)->nullable(); // Commission value
            
            // Insurance and Legal Documents
            $table->string('insurance_provider'); // Insurance company name
            $table->string('policy_number'); // Insurance policy number
            $table->date('insurance_expiry_date'); // Insurance expiration date
            $table->string('insurance_document_path')->nullable(); // File path for insurance document
            $table->string('rc_number'); // RC Number
            $table->string('rc_document_path')->nullable(); // File path for RC document
            
            // Maintenance and Service
            $table->date('last_service_date')->nullable(); // Last service date
            $table->integer('last_service_meter_reading')->nullable(); // Meter reading at last service
            $table->date('next_service_due')->nullable(); // Next expected service date
            $table->integer('next_service_meter_reading')->nullable(); // Expected meter reading for next service
            
            // Additional Information
            $table->text('remarks_notes')->nullable(); // Special remarks, notes, or instructions
            
            // Vehicle availability toggle
            $table->boolean('is_available')->default(true);
            $table->date('unavailable_from')->nullable();
            $table->date('unavailable_until')->nullable();
            
            // Indexes for better performance
            $table->index(['business_id', 'vehicle_type']);
            $table->index(['vehicle_status', 'is_available']);
            $table->index('vehicle_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            //
        });
    }
};
