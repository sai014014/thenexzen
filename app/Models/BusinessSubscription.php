<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BusinessSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'subscription_package_id',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'trial_ends_at',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'cancellation_reason',
        'auto_renew',
        'used_features',
        'module_access',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'auto_renew' => 'boolean',
        'used_features' => 'array',
        'module_access' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function subscriptionPackage(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial')
                    ->where('trial_ends_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->orWhere('status', 'expired');
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    public function getIsTrialAttribute()
    {
        return $this->status === 'trial' && $this->trial_ends_at > now();
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at < now() || $this->status === 'expired';
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->is_trial) {
            return max(0, $this->trial_ends_at->diffInDays(now()));
        }
        return max(0, $this->expires_at->diffInDays(now()));
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'trial' => 'Trial',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
            'pending' => 'Pending',
            default => 'Unknown'
        };
    }

    // Methods
    public function canAccessModule($module)
    {
        if (!$this->is_active && !$this->is_trial) {
            return false;
        }

        // First check the subscription's module_access
        $moduleAccess = $this->module_access ?? [];
        if (in_array($module, $moduleAccess)) {
            return true;
        }

        // Fallback to package modules if subscription modules are empty
        $package = $this->subscriptionPackage;
        if ($package) {
            $packageModules = $package->enabled_modules ?? [];
            return in_array($module, $packageModules);
        }

        return false;
    }

    public function canUseFeature($feature)
    {
        if (!$this->is_active && !$this->is_trial) {
            return false;
        }

        $package = $this->subscriptionPackage;
        if (!$package) {
            return false;
        }

        return $package->{$feature} ?? false;
    }

    public function getAvailableModules()
    {
        if (!$this->is_active && !$this->is_trial) {
            return [];
        }

        // Return subscription modules if available
        $moduleAccess = $this->module_access ?? [];
        if (!empty($moduleAccess)) {
            return $moduleAccess;
        }

        // Fallback to package modules
        $package = $this->subscriptionPackage;
        if ($package) {
            return $package->enabled_modules ?? [];
        }

        return [];
    }

    public function syncWithPackage()
    {
        $package = $this->subscriptionPackage;
        if (!$package) {
            return false;
        }

        $this->update([
            'module_access' => $package->enabled_modules ?? []
        ]);

        return true;
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'auto_renew' => false,
        ]);
    }

    public function renew($newExpiryDate = null)
    {
        $expiresAt = $newExpiryDate ? Carbon::parse($newExpiryDate) : $this->expires_at->addMonth();
        
        $this->update([
            'status' => 'active',
            'expires_at' => $expiresAt,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);
    }

    // Vehicle Capacity Methods
    public function getVehicleCapacity()
    {
        if (!$this->is_active && !$this->is_trial) {
            return 0;
        }

        $package = $this->subscriptionPackage;
        if (!$package) {
            return 0;
        }

        return $package->is_unlimited_vehicles ? -1 : ($package->vehicle_capacity ?? 0);
    }

    public function getCurrentVehicleCount()
    {
        return $this->business->vehicles()->count();
    }

    public function canAddVehicle()
    {
        $capacity = $this->getVehicleCapacity();
        
        // Unlimited capacity
        if ($capacity === -1) {
            return true;
        }

        // Check if current count is less than capacity
        return $this->getCurrentVehicleCount() < $capacity;
    }

    public function getRemainingVehicleSlots()
    {
        $capacity = $this->getVehicleCapacity();
        
        // Unlimited capacity
        if ($capacity === -1) {
            return 'Unlimited';
        }

        $current = $this->getCurrentVehicleCount();
        $remaining = max(0, $capacity - $current);
        
        return $remaining;
    }

    public function getVehicleCapacityStatus()
    {
        $capacity = $this->getVehicleCapacity();
        $current = $this->getCurrentVehicleCount();
        
        if ($capacity === -1) {
            return [
                'unlimited' => true,
                'current' => $current,
                'remaining' => 'Unlimited',
                'can_add' => true,
                'message' => "You can add unlimited vehicles. Current: {$current}"
            ];
        }

        $remaining = max(0, $capacity - $current);
        $canAdd = $remaining > 0;
        
        return [
            'unlimited' => false,
            'current' => $current,
            'capacity' => $capacity,
            'remaining' => $remaining,
            'can_add' => $canAdd,
            'message' => $canAdd 
                ? "You can add {$remaining} more vehicle(s). Current: {$current}/{$capacity}"
                : "Vehicle limit reached ({$current}/{$capacity}). Please contact admin or upgrade your package."
        ];
    }
}
