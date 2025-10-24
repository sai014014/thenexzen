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
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name');
            $table->decimal('subscription_fee', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->integer('trial_period_days')->default(14);
            $table->decimal('onboarding_fee', 10, 2)->default(6000);
            $table->integer('vehicle_capacity')->nullable(); // null means unlimited
            $table->boolean('is_unlimited_vehicles')->default(false);
            
            // Features & Functionalities
            $table->boolean('booking_management')->default(true);
            $table->boolean('customer_management')->default(true);
            $table->boolean('vehicle_management')->default(true);
            $table->boolean('basic_reporting')->default(true);
            $table->boolean('advanced_reporting')->default(false);
            $table->boolean('vendor_management')->default(false);
            $table->boolean('maintenance_reminders')->default(true);
            $table->boolean('customization_options')->default(false);
            $table->boolean('multi_user_access')->default(false);
            $table->boolean('dedicated_account_manager')->default(false);
            $table->enum('support_type', ['standard', 'chat_only', 'full_support', 'enterprise_level'])->default('standard');
            
            // Subscription Settings
            $table->json('billing_cycles'); // ['monthly', 'quarterly', 'yearly', 'custom']
            $table->json('payment_methods'); // ['direct_debit', 'credit_card', 'bank_transfer', 'cash']
            $table->enum('renewal_type', ['auto_renew', 'manual_renewal'])->default('auto_renew');
            
            // Package Availability
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->boolean('show_on_website')->default(true);
            $table->boolean('internal_use_only')->default(false);
            
            $table->text('description')->nullable();
            $table->text('features_summary')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'show_on_website']);
            $table->index('package_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};
