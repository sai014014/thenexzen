<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'user_name',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the business that owns the activity log.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(BusinessAdmin::class, 'user_id');
    }

    /**
     * Get the model that was affected by the action.
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Get formatted action description.
     */
    public function getFormattedActionAttribute(): string
    {
        return match($this->action) {
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'booking_created' => 'Created Booking',
            'booking_updated' => 'Updated Booking',
            'booking_cancelled' => 'Cancelled Booking',
            'vehicle_added' => 'Added Vehicle',
            'vehicle_updated' => 'Updated Vehicle',
            'vehicle_deleted' => 'Deleted Vehicle',
            'customer_added' => 'Added Customer',
            'customer_updated' => 'Updated Customer',
            'customer_deleted' => 'Deleted Customer',
            'vendor_added' => 'Added Vendor',
            'vendor_updated' => 'Updated Vendor',
            'vendor_deleted' => 'Deleted Vendor',
            'user_added' => 'Added User',
            'user_updated' => 'Updated User',
            'user_deleted' => 'Deleted User',
            'password_changed' => 'Changed Password',
            'profile_updated' => 'Updated Profile',
            'subscription_started' => 'Started Subscription',
            'subscription_paused' => 'Paused Subscription',
            'subscription_resumed' => 'Resumed Subscription',
            'subscription_cancelled' => 'Cancelled Subscription',
            default => ucfirst(str_replace('_', ' ', $this->action))
        };
    }

    /**
     * Get action icon.
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'login' => 'fas fa-sign-in-alt text-success',
            'logout' => 'fas fa-sign-out-alt text-warning',
            'booking_created' => 'fas fa-plus-circle text-primary',
            'booking_updated' => 'fas fa-edit text-info',
            'booking_cancelled' => 'fas fa-times-circle text-danger',
            'vehicle_added' => 'fas fa-car text-success',
            'vehicle_updated' => 'fas fa-car text-info',
            'vehicle_deleted' => 'fas fa-car text-danger',
            'customer_added' => 'fas fa-user-plus text-success',
            'customer_updated' => 'fas fa-user-edit text-info',
            'customer_deleted' => 'fas fa-user-times text-danger',
            'vendor_added' => 'fas fa-truck text-success',
            'vendor_updated' => 'fas fa-truck text-info',
            'vendor_deleted' => 'fas fa-truck text-danger',
            'user_added' => 'fas fa-user-plus text-success',
            'user_updated' => 'fas fa-user-edit text-info',
            'user_deleted' => 'fas fa-user-times text-danger',
            'password_changed' => 'fas fa-key text-warning',
            'profile_updated' => 'fas fa-user-cog text-info',
            'subscription_started' => 'fas fa-play text-success',
            'subscription_paused' => 'fas fa-pause text-warning',
            'subscription_resumed' => 'fas fa-play text-success',
            'subscription_cancelled' => 'fas fa-stop text-danger',
            default => 'fas fa-circle text-secondary'
        };
    }
}