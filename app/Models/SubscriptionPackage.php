<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'package_name',
        'subscription_fee',
        'currency',
        'trial_period_days',
        'onboarding_fee',
        'vehicle_capacity',
        'is_unlimited_vehicles',
        'booking_management',
        'customer_management',
        'vehicle_management',
        'basic_reporting',
        'advanced_reporting',
        'vendor_management',
        'maintenance_reminders',
        'customization_options',
        'multi_user_access',
        'dedicated_account_manager',
        'support_type',
        'billing_cycles',
        'payment_methods',
        'renewal_type',
        'status',
        'show_on_website',
        'internal_use_only',
        'description',
        'features_summary',
    ];

    protected $casts = [
        'subscription_fee' => 'decimal:2',
        'onboarding_fee' => 'decimal:2',
        'trial_period_days' => 'integer',
        'vehicle_capacity' => 'integer',
        'is_unlimited_vehicles' => 'boolean',
        'booking_management' => 'boolean',
        'customer_management' => 'boolean',
        'vehicle_management' => 'boolean',
        'basic_reporting' => 'boolean',
        'advanced_reporting' => 'boolean',
        'vendor_management' => 'boolean',
        'maintenance_reminders' => 'boolean',
        'customization_options' => 'boolean',
        'multi_user_access' => 'boolean',
        'dedicated_account_manager' => 'boolean',
        'show_on_website' => 'boolean',
        'internal_use_only' => 'boolean',
        'billing_cycles' => 'array',
        'payment_methods' => 'array',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('show_on_website', true);
    }

    public function scopeInternal($query)
    {
        return $query->where('internal_use_only', true);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->subscription_fee, 2);
    }

    public function getFormattedOnboardingFeeAttribute()
    {
        return $this->currency . ' ' . number_format($this->onboarding_fee, 2);
    }

    public function getVehicleCapacityDisplayAttribute()
    {
        return $this->is_unlimited_vehicles ? 'Unlimited' : $this->vehicle_capacity;
    }

    public function getSupportTypeDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->support_type));
    }

    public function getRenewalTypeDisplayAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->renewal_type));
    }

    // Methods
    public function getFeaturesList()
    {
        $features = [];
        
        if ($this->booking_management) $features[] = 'Booking Management';
        if ($this->customer_management) $features[] = 'Customer Management';
        if ($this->vehicle_management) $features[] = 'Vehicle Management';
        if ($this->basic_reporting) $features[] = 'Basic Reporting';
        if ($this->advanced_reporting) $features[] = 'Advanced Reporting & Analytics';
        if ($this->vendor_management) $features[] = 'Vendor Management';
        if ($this->maintenance_reminders) $features[] = 'Vehicle Maintenance Reminders';
        if ($this->customization_options) $features[] = 'Customization Options';
        if ($this->multi_user_access) $features[] = 'Multi-User Access & Role-Based Permissions';
        if ($this->dedicated_account_manager) $features[] = 'Dedicated Account Manager';
        
        return $features;
    }

    public function getBillingCyclesDisplay()
    {
        $cycles = [];
        foreach ($this->billing_cycles as $cycle) {
            $cycles[] = ucfirst($cycle);
        }
        return implode(', ', $cycles);
    }

    public function getPaymentMethodsDisplay()
    {
        $methods = [];
        foreach ($this->payment_methods as $method) {
            $methods[] = ucwords(str_replace('_', ' ', $method));
        }
        return implode(', ', $methods);
    }
}
