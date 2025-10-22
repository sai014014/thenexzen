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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->enum('customer_type', ['individual', 'corporate']);
            
            // Basic Information
            $table->string('full_name');
            $table->string('mobile_number', 15);
            $table->string('alternate_contact_number', 15)->nullable();
            $table->string('email_address')->nullable();
            $table->date('date_of_birth')->nullable(); // Only for individuals
            
            // Address Information
            $table->text('permanent_address');
            $table->text('current_address')->nullable();
            $table->boolean('same_as_permanent')->default(false);
            
            // Identity Information
            $table->enum('government_id_type', ['aadhar_card', 'passport', 'pan_card', 'voter_id'])->nullable();
            $table->string('government_id_number')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->string('driving_license_path')->nullable();
            $table->date('license_expiry_date')->nullable();
            
            // Additional Contact Information
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number', 15)->nullable();
            $table->text('additional_information')->nullable();
            
            // Corporate-specific fields
            $table->string('company_name')->nullable();
            $table->enum('company_type', ['private_limited', 'public_limited', 'partnership', 'proprietorship', 'llp', 'ngo', 'other'])->nullable();
            $table->string('gstin', 15)->nullable();
            $table->text('company_address')->nullable();
            $table->string('pan_number', 10)->nullable();
            
            // Primary Contact Person (for corporate)
            $table->string('contact_person_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('official_email')->nullable();
            $table->string('contact_person_mobile', 15)->nullable();
            $table->string('contact_person_alternate', 15)->nullable();
            
            // Invoicing and Payment Preferences (for corporate)
            $table->string('billing_name')->nullable();
            $table->string('billing_email')->nullable();
            $table->text('billing_address')->nullable();
            $table->enum('preferred_payment_method', ['bank_transfer', 'upi', 'corporate_credit_card', 'cheque_payment', 'cash', 'card'])->nullable();
            $table->enum('invoice_frequency', ['weekly', 'monthly'])->nullable();
            
            // Status and metadata
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'customer_type']);
            $table->index(['mobile_number']);
            $table->index(['email_address']);
            $table->index(['status']);
            $table->index(['gstin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};