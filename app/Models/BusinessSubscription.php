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
        'is_paused',
        'paused_at',
        'resumed_at',
        'paused_days',
        'pause_reason',
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
        'is_paused' => 'boolean',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
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
        
        if (!$this->expires_at) {
            return 0;
        }
        
        $now = Carbon::now();
        $expiresAt = Carbon::parse($this->expires_at);
        
        if ($this->is_paused && $this->paused_at) {
            // If paused, calculate days remaining from when it was paused
            $pausedAt = Carbon::parse($this->paused_at);
            $daysSincePaused = $now->diffInDays($pausedAt);
            $originalDaysRemaining = $pausedAt->diffInDays($expiresAt);
            return max(0, $originalDaysRemaining - $daysSincePaused);
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

    /**
     * Pause the subscription
     */
    public function pause($reason = null)
    {
        if ($this->status !== 'active' || $this->is_paused) {
            return false;
        }

        $this->update([
            'is_paused' => true,
            'paused_at' => now(),
            'pause_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Resume the subscription
     */
    public function resume()
    {
        if (!$this->is_paused) {
            return false;
        }

        // Calculate paused days (round to avoid decimal issues)
        $pausedDays = $this->paused_at ? round($this->paused_at->diffInDays(now())) : 0;
        $totalPausedDays = $this->paused_days + $pausedDays;

        // Extend expiry date by the paused days
        $newExpiryDate = $this->expires_at->copy()->addDays($pausedDays);

        $this->update([
            'is_paused' => false,
            'resumed_at' => now(),
            'paused_days' => $totalPausedDays,
            'expires_at' => $newExpiryDate,
        ]);

        return true;
    }

    /**
     * Check if subscription is paused
     */
    public function isPaused()
    {
        return $this->is_paused;
    }

    /**
     * Get remaining days considering pause
     */
    public function getRemainingDays()
    {
        if ($this->is_paused) {
            return $this->expires_at->diffInDays(now());
        }
        
        return $this->expires_at->diffInDays(now());
    }

    /**
     * Get pause status information
     */
    public function getPauseStatus()
    {
        if (!$this->is_paused) {
            return [
                'is_paused' => false,
                'paused_days' => $this->paused_days,
                'total_paused_days' => $this->paused_days,
            ];
        }

        $currentPausedDays = $this->paused_at ? round($this->paused_at->diffInDays(now())) : 0;
        $totalPausedDays = $this->paused_days + $currentPausedDays;

        return [
            'is_paused' => true,
            'paused_at' => $this->paused_at,
            'paused_days' => $currentPausedDays,
            'total_paused_days' => $totalPausedDays,
            'pause_reason' => $this->pause_reason,
        ];
    }

    /**
     * Get vehicle limit for this subscription
     */
    public function getVehicleLimit()
    {
        if (!$this->is_active && !$this->is_trial) {
            return 0;
        }

        $package = $this->subscriptionPackage;
        if (!$package) {
            return 0;
        }

        return $package->is_unlimited_vehicles ? 'Unlimited' : ($package->vehicle_capacity ?? 0);
    }

    /**
     * Get booking limit for this subscription
     */
    public function getBookingLimit()
    {
        if (!$this->is_active && !$this->is_trial) {
            return 0;
        }

        $package = $this->subscriptionPackage;
        if (!$package) {
            return 0;
        }

        // For now, return unlimited for bookings as we don't have a specific booking limit field
        return 'Unlimited';
    }

    /**
     * Get user limit for this subscription
     */
    public function getUserLimit()
    {
        if (!$this->is_active && !$this->is_trial) {
            return 0;
        }

        $package = $this->subscriptionPackage;
        if (!$package) {
            return 0;
        }

        // Check if multi-user access is enabled
        if ($package->multi_user_access) {
            return 'Unlimited';
        }

        // Default to 1 user if multi-user access is not enabled
        return 1;
    }
}
