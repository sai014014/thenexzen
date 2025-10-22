<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            
            // Vendor Information
            $table->string('vendor_name');
            $table->enum('vendor_type', ['vehicle_provider', 'service_partner', 'other']);
            $table->string('gstin', 15)->nullable();
            $table->string('pan_number', 10);
            $table->string('primary_contact_person');
            
            // Contact Information
            $table->string('mobile_number', 15);
            $table->string('alternate_contact_number', 15)->nullable();
            $table->string('email_address');
            
            // Address Information
            $table->text('office_address');
            $table->json('additional_branches')->nullable(); // Store as JSON array of addresses
            
            // Vendor Payout Settings
            $table->enum('payout_method', ['bank_transfer', 'upi_payment', 'cheque', 'other']);
            $table->string('other_payout_method')->nullable(); // If 'other' is selected
            
            // Bank Details (if Bank Transfer selected)
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code', 11)->nullable();
            $table->string('bank_branch_name')->nullable();
            
            // UPI Payment Details (if UPI Payment selected)
            $table->string('upi_id')->nullable();
            
            // Payout Frequency
            $table->enum('payout_frequency', ['weekly', 'bi_weekly', 'monthly', 'quarterly', 'after_every_booking']);
            $table->enum('payout_day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->integer('payout_day_of_month')->nullable(); // 1-31 for monthly/quarterly
            
            // Payout Terms
            $table->text('payout_terms')->nullable();
            
            // Commission Settings
            $table->enum('commission_type', ['fixed_amount', 'percentage_of_revenue']);
            $table->decimal('commission_rate', 10, 2); // Can store both fixed amount and percentage
            
            // Document Uploads
            $table->string('vendor_agreement_path')->nullable();
            $table->string('gstin_certificate_path')->nullable();
            $table->string('pan_card_path')->nullable();
            $table->json('additional_certificates')->nullable(); // Store as JSON array of file paths
            
            // Status and metadata
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'vendor_type']);
            $table->index(['mobile_number']);
            $table->index(['email_address']);
            $table->index(['gstin']);
            $table->index(['pan_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};