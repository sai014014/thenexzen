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
                // Make Laravel notification fields nullable for custom notifications
                $table->string('type')->nullable()->change();
                $table->string('notifiable_type')->nullable()->change();
                $table->unsignedBigInteger('notifiable_id')->nullable()->change();
                $table->text('data')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                // Note: Cannot make these NOT NULL again if there are NULL values
                // This is a one-way migration for safety
            });
        }
    }
};

