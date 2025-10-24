<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'business_slug',
        'business_type',
        'description',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'logo',
        'business_settings',
        'status',
        'subscription_expires_at',
        'subscription_plan',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'business_settings' => 'array',
        'subscription_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($business) {
            if (empty($business->business_slug)) {
                $business->business_slug = Str::slug($business->business_name);
            }
        });
    }

    public function businessAdmins()
    {
        return $this->hasMany(BusinessAdmin::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(BusinessSubscription::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isVerified()
    {
        return $this->is_verified;
    }

    public function hasActiveSubscription()
    {
        return $this->subscription_expires_at && $this->subscription_expires_at->isFuture();
    }
}
