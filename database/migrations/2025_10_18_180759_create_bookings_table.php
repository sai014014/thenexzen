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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            
            // Booking Details
            $table->string('booking_number')->unique(); // Auto-generated booking ID
            $table->datetime('start_date_time');
            $table->datetime('end_date_time');
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            
            // Pricing Information
            $table->decimal('base_rental_price', 10, 2); // 24-hour base price
            $table->decimal('extra_charges', 10, 2)->default(0); // Additional charges
            $table->decimal('total_amount', 10, 2); // Total amount due
            $table->decimal('amount_paid', 10, 2)->default(0); // Amount already paid
            $table->decimal('amount_due', 10, 2); // Amount still due
            
            // Payment Information
            $table->decimal('advance_amount', 10, 2)->default(0); // Advance payment made
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'upi', 'bank_transfer', 'cheque'])->nullable();
            $table->enum('advance_payment_method', ['cash', 'credit_card', 'debit_card', 'upi', 'bank_transfer', 'cheque'])->nullable();
            
            // Additional Information
            $table->text('notes')->nullable(); // Operator notes
            $table->text('customer_notes')->nullable(); // Customer special requests
            $table->text('cancellation_reason')->nullable(); // Reason for cancellation
            
            // Timestamps
            $table->timestamp('started_at')->nullable(); // When booking actually started
            $table->timestamp('completed_at')->nullable(); // When booking was completed
            $table->timestamp('cancelled_at')->nullable(); // When booking was cancelled
            $table->timestamps();
            
            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['customer_id']);
            $table->index(['vehicle_id']);
            $table->index(['start_date_time']);
            $table->index(['end_date_time']);
            $table->index(['booking_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};