<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'vendor_name',
        'vendor_type',
        'gstin',
        'pan_number',
        'primary_contact_person',
        'mobile_number',
        'alternate_contact_number',
        'email_address',
        'office_address',
        'additional_branches',
        'payout_method',
        'other_payout_method',
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'bank_branch_name',
        'upi_id',
        'payout_frequency',
        'payout_day_of_week',
        'payout_day_of_month',
        'payout_terms',
        'commission_type',
        'commission_rate',
        'vendor_agreement_path',
        'gstin_certificate_path',
        'pan_card_path',
        'additional_certificates',
        'status',
    ];

    protected $casts = [
        'additional_branches' => 'array',
        'additional_certificates' => 'array',
        'commission_rate' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * Get the business that owns the vendor.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the vendor type label.
     */
    public function getVendorTypeLabelAttribute(): string
    {
        return match($this->vendor_type) {
            'vehicle_provider' => 'Vehicle Provider',
            'service_partner' => 'Service Partner',
            'other' => 'Other',
            default => 'Unknown'
        };
    }

    /**
     * Get the payout method label.
     */
    public function getPayoutMethodLabelAttribute(): string
    {
        return match($this->payout_method) {
            'bank_transfer' => 'Bank Transfer (NEFT/RTGS)',
            'upi_payment' => 'UPI Payment',
            'cheque' => 'Cheque',
            'other' => $this->other_payout_method ?? 'Other',
            default => 'Unknown'
        };
    }

    /**
     * Get the payout frequency label.
     */
    public function getPayoutFrequencyLabelAttribute(): string
    {
        return match($this->payout_frequency) {
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'after_every_booking' => 'After Every Booking',
            default => 'Unknown'
        };
    }

    /**
     * Get the commission type label.
     */
    public function getCommissionTypeLabelAttribute(): string
    {
        return match($this->commission_type) {
            'fixed_amount' => 'Fixed Amount',
            'percentage_of_revenue' => 'Percentage of Revenue',
            default => 'Unknown'
        };
    }

    /**
     * Get the commission rate with proper formatting.
     */
    public function getFormattedCommissionRateAttribute(): string
    {
        if ($this->commission_type === 'percentage_of_revenue') {
            return $this->commission_rate . '%';
        }
        return '₹' . number_format($this->commission_rate, 2);
    }

    /**
     * Get the payout schedule description.
     */
    public function getPayoutScheduleAttribute(): string
    {
        $schedule = $this->payout_frequency_label;
        
        if ($this->payout_frequency === 'weekly' || $this->payout_frequency === 'bi_weekly') {
            if ($this->payout_day_of_week) {
                $schedule .= ' (' . ucfirst($this->payout_day_of_week) . ')';
            }
        } elseif (in_array($this->payout_frequency, ['monthly', 'quarterly'])) {
            if ($this->payout_day_of_month) {
                $schedule .= ' (Day ' . $this->payout_day_of_month . ')';
            }
        }
        
        return $schedule;
    }

    /**
     * Get masked PAN number.
     */
    public function getMaskedPanNumberAttribute(): string
    {
        if (!$this->pan_number) {
            return '';
        }
        
        $length = strlen($this->pan_number);
        if ($length <= 4) {
            return $this->pan_number;
        }
        
        return str_repeat('X', $length - 4) . substr($this->pan_number, -4);
    }

    /**
     * Get masked GSTIN.
     */
    public function getMaskedGstinAttribute(): string
    {
        if (!$this->gstin) {
            return '';
        }
        
        $length = strlen($this->gstin);
        if ($length <= 4) {
            return $this->gstin;
        }
        
        return str_repeat('X', $length - 4) . substr($this->gstin, -4);
    }

    /**
     * Get masked account number.
     */
    public function getMaskedAccountNumberAttribute(): string
    {
        if (!$this->account_number) {
            return '';
        }
        
        $length = strlen($this->account_number);
        if ($length <= 4) {
            return $this->account_number;
        }
        
        return str_repeat('X', $length - 4) . substr($this->account_number, -4);
    }

    /**
     * Get the full path for a document.
     */
    public function getDocumentPath(string $documentType): ?string
    {
        $path = match($documentType) {
            'vendor_agreement' => $this->vendor_agreement_path,
            'gstin_certificate' => $this->gstin_certificate_path,
            'pan_card' => $this->pan_card_path,
            default => null
        };
        
        return $path ? Storage::disk('public')->path($path) : null;
    }

    /**
     * Get additional certificates as array of file paths.
     */
    public function getAdditionalCertificatesPathsAttribute(): array
    {
        if (!$this->additional_certificates) {
            return [];
        }
        
        return array_map(function($path) {
            return Storage::disk('public')->path($path);
        }, $this->additional_certificates);
    }

    /**
     * Check if vendor has bank details.
     */
    public function hasBankDetails(): bool
    {
        return $this->payout_method === 'bank_transfer' && 
               $this->bank_name && 
               $this->account_holder_name && 
               $this->account_number && 
               $this->ifsc_code;
    }

    /**
     * Check if vendor has UPI details.
     */
    public function hasUpiDetails(): bool
    {
        return $this->payout_method === 'upi_payment' && $this->upi_id;
    }

    /**
     * Get display name for the vendor.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->vendor_name;
    }
}