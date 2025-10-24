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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('category', [
                'service_reminder',
                'insurance_renewal', 
                'booking_reminder',
                'maintenance',
                'inspection',
                'general'
            ]);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->datetime('due_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_completed')->default(false);
            $table->datetime('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('business_admins')->onDelete('set null');
            $table->text('completion_notes')->nullable();
            $table->datetime('snooze_until')->nullable();
            $table->datetime('snoozed_at')->nullable();
            $table->foreignId('snoozed_by')->nullable()->constrained('business_admins')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['business_id', 'is_active', 'is_completed']);
            $table->index(['due_date', 'is_completed']);
            $table->index(['category', 'priority']);
            $table->index(['snooze_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
