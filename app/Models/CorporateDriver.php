<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateDriver extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'driver_name',
        'driving_license_number',
        'license_expiry_date',
        'driving_license_path',
    ];

    protected $casts = [
        'license_expiry_date' => 'date',
    ];

    /**
     * Get the customer that owns the corporate driver.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if the driving license is expired.
     */
    public function isLicenseExpired(): bool
    {
        return $this->license_expiry_date < now();
    }

    /**
     * Check if the driving license is near expiry (within 30 days).
     */
    public function isLicenseNearExpiry(): bool
    {
        return $this->license_expiry_date <= now()->addDays(30) && $this->license_expiry_date > now();
    }

    /**
     * Get the license status.
     */
    public function getLicenseStatusAttribute(): string
    {
        if ($this->isLicenseExpired()) {
            return 'expired';
        }

        if ($this->isLicenseNearExpiry()) {
            return 'near_expiry';
        }

        return 'valid';
    }

    /**
     * Scope for expired licenses.
     */
    public function scopeWithExpiredLicenses($query)
    {
        return $query->where('license_expiry_date', '<', now());
    }

    /**
     * Scope for licenses near expiry.
     */
    public function scopeWithLicensesNearExpiry($query)
    {
        return $query->where('license_expiry_date', '<=', now()->addDays(30))
                    ->where('license_expiry_date', '>', now());
    }
}