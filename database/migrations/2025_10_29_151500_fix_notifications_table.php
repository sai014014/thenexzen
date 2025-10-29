<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If custom table exists and default does not, rename it
        if (Schema::hasTable('laravel_notifications') && !Schema::hasTable('notifications')) {
            Schema::rename('laravel_notifications', 'notifications');
        }

        // Ensure required columns exist on notifications table
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'id')) {
                    $table->uuid('id')->primary();
                }
                if (!Schema::hasColumn('notifications', 'type')) {
                    $table->string('type')->nullable(false)->after('id');
                }
                if (!Schema::hasColumn('notifications', 'notifiable_type') || !Schema::hasColumn('notifications', 'notifiable_id')) {
                    $table->morphs('notifiable');
                }
                if (!Schema::hasColumn('notifications', 'data')) {
                    $table->text('data');
                }
                if (!Schema::hasColumn('notifications', 'read_at')) {
                    $table->timestamp('read_at')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'created_at')) {
                    $table->timestamp('created_at')->useCurrent();
                }
                if (!Schema::hasColumn('notifications', 'updated_at')) {
                    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                }
            });
        } else {
            // Create notifications table if absent
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // No destructive rollback to avoid data loss
    }
};


