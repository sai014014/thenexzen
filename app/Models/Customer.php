<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'customer_type',
        'full_name',
        'mobile_number',
        'alternate_contact_number',
        'email_address',
        'date_of_birth',
        'permanent_address',
        'current_address',
        'same_as_permanent',
        'government_id_type',
        'government_id_number',
        'driving_license_number',
        'driving_license_path',
        'license_expiry_date',
        'emergency_contact_name',
        'emergency_contact_number',
        'additional_information',
        'company_name',
        'company_type',
        'gstin',
        'company_address',
        'pan_number',
        'contact_person_name',
        'designation',
        'official_email',
        'contact_person_mobile',
        'contact_person_alternate',
        'billing_name',
        'billing_email',
        'billing_address',
        'preferred_payment_method',
        'invoice_frequency',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'license_expiry_date' => 'date',
        'same_as_permanent' => 'boolean',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * Get the business that owns the customer.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the corporate drivers for this customer.
     */
    public function corporateDrivers(): HasMany
    {
        return $this->hasMany(CorporateDriver::class);
    }

    /**
     * Get the bookings for the customer.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the display name based on customer type.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->customer_type === 'corporate' ? $this->company_name : $this->full_name;
    }

    /**
     * Get the primary contact number.
     */
    public function getPrimaryContactAttribute(): string
    {
        return $this->customer_type === 'corporate' ? $this->contact_person_mobile : $this->mobile_number;
    }

    /**
     * Get the primary email.
     */
    public function getPrimaryEmailAttribute(): string
    {
        return $this->customer_type === 'corporate' ? $this->official_email : $this->email_address;
    }

    /**
     * Get the masked government ID number.
     */
    public function getMaskedGovernmentIdAttribute(): string
    {
        if (!$this->government_id_number) {
            return '';
        }

        $id = $this->government_id_number;
        $length = strlen($id);
        
        if ($length <= 4) {
            return str_repeat('X', $length);
        }

        return str_repeat('X', $length - 4) . ' ' . substr($id, -4);
    }

    /**
     * Get the masked GSTIN.
     */
    public function getMaskedGstinAttribute(): string
    {
        if (!$this->gstin) {
            return '';
        }

        $gstin = $this->gstin;
        $length = strlen($gstin);
        
        if ($length <= 4) {
            return str_repeat('X', $length);
        }

        return str_repeat('X', $length - 4) . ' ' . substr($gstin, -4);
    }

    /**
     * Get the masked PAN number.
     */
    public function getMaskedPanAttribute(): string
    {
        if (!$this->pan_number) {
            return '';
        }

        $pan = $this->pan_number;
        $length = strlen($pan);
        
        if ($length <= 4) {
            return str_repeat('X', $length);
        }

        return str_repeat('X', $length - 4) . ' ' . substr($pan, -4);
    }

    /**
     * Check if the customer is an individual.
     */
    public function isIndividual(): bool
    {
        return $this->customer_type === 'individual';
    }

    /**
     * Check if the customer is corporate.
     */
    public function isCorporate(): bool
    {
        return $this->customer_type === 'corporate';
    }

    /**
     * Check if the driving license is expired.
     */
    public function isLicenseExpired(): bool
    {
        if (!$this->license_expiry_date) {
            return false;
        }

        return $this->license_expiry_date < now();
    }

    /**
     * Check if the driving license is near expiry (within 30 days).
     */
    public function isLicenseNearExpiry(): bool
    {
        if (!$this->license_expiry_date) {
            return false;
        }

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
     * Scope for individual customers.
     */
    public function scopeIndividuals($query)
    {
        return $query->where('customer_type', 'individual');
    }

    /**
     * Scope for corporate customers.
     */
    public function scopeCorporate($query)
    {
        return $query->where('customer_type', 'corporate');
    }

    /**
     * Scope for active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive customers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for pending customers.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for customers with expired licenses.
     */
    public function scopeWithExpiredLicenses($query)
    {
        return $query->where('license_expiry_date', '<', now());
    }

    /**
     * Scope for customers with licenses near expiry.
     */
    public function scopeWithLicensesNearExpiry($query)
    {
        return $query->where('license_expiry_date', '<=', now()->addDays(30))
                    ->where('license_expiry_date', '>', now());
    }
}