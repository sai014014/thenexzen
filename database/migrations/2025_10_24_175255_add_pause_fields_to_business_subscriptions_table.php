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
        Schema::table('business_subscriptions', function (Blueprint $table) {
            $table->boolean('is_paused')->default(false)->after('auto_renew');
            $table->timestamp('paused_at')->nullable()->after('is_paused');
            $table->timestamp('resumed_at')->nullable()->after('paused_at');
            $table->integer('paused_days')->default(0)->after('resumed_at');
            $table->text('pause_reason')->nullable()->after('paused_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['is_paused', 'paused_at', 'resumed_at', 'paused_days', 'pause_reason']);
        });
    }
};