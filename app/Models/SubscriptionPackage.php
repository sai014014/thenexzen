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
        'billing_cycles',
        'payment_methods',
        'renewal_type',
        'support_type',
        'show_on_website',
        'internal_use_only',
        'status',
        'description',
        'features_summary',
        'enabled_modules',
    ];

    protected $casts = [
        'subscription_fee' => 'decimal:2',
        'onboarding_fee' => 'decimal:2',
        'trial_period_days' => 'integer',
        'vehicle_capacity' => 'integer',
        'is_unlimited_vehicles' => 'boolean',
        'billing_cycles' => 'array',
        'payment_methods' => 'array',
        'show_on_website' => 'boolean',
        'internal_use_only' => 'boolean',
        'enabled_modules' => 'array',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Relationships
    public function businessSubscriptions()
    {
        return $this->hasMany(BusinessSubscription::class);
    }

    public function activeBusinessSubscriptions()
    {
        return $this->hasMany(BusinessSubscription::class)->whereIn('status', ['active', 'trial']);
    }

    // Methods
    public function getEnabledModules()
    {
        return $this->enabled_modules ?? [];
    }

    public function hasModule($module)
    {
        $modules = $this->getEnabledModules();
        return in_array($module, $modules);
    }

    public function getAvailableModules()
    {
        return [
            'vehicles' => 'Vehicle Management',
            'bookings' => 'Booking Management',
            'customers' => 'Customer Management',
            'vendors' => 'Vendor Management',
            'reports' => 'Reports & Analytics',
            'notifications' => 'Notifications',
            'subscription' => 'Subscription Management',
        ];
    }

    public function getModuleDisplayName($module)
    {
        $modules = $this->getAvailableModules();
        return $modules[$module] ?? ucfirst(str_replace('_', ' ', $module));
    }

    // Additional helper methods for views
    public function getFormattedPriceAttribute()
    {
        $symbol = $this->currency === 'INR' ? '₹' : ($this->currency === 'USD' ? '$' : '€');
        return $symbol . ' ' . number_format($this->subscription_fee, 2);
    }

    public function getFormattedOnboardingFeeAttribute()
    {
        $symbol = $this->currency === 'INR' ? '₹' : ($this->currency === 'USD' ? '$' : '€');
        return $symbol . ' ' . number_format($this->onboarding_fee, 2);
    }

    public function getVehicleCapacityDisplayAttribute()
    {
        return $this->is_unlimited_vehicles ? 'Unlimited' : ($this->vehicle_capacity ?? '0');
    }

    public function getActiveBusinessCountAttribute()
    {
        return $this->activeBusinessSubscriptions()->count();
    }

    public function canBeDeactivated()
    {
        return $this->activeBusinessSubscriptions()->count() === 0;
    }

    public function getActiveBusinesses()
    {
        return $this->activeBusinessSubscriptions()->with('business')->get();
    }
}
