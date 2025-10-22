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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('business_slug')->unique();
            $table->string('business_type')->default('car_dealership'); // car_dealership, car_rental, car_service, etc.
            $table->text('description')->nullable();
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('postal_code');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->json('business_settings')->nullable(); // Store business-specific settings
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->string('subscription_plan')->default('basic');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('businesses');
    }
};
