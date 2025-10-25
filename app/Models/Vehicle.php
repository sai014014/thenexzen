<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'vehicle_type',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'vehicle_number',
        'vin_number',
        'vehicle_status',
        'fuel_type',
        'mileage',
        'transmission_type',
        'seating_capacity',
        'engine_capacity_cc',
        'payload_capacity_tons',
        'rental_price_24h',
        'km_limit_per_booking',
        'extra_rental_price_per_hour',
        'extra_price_per_km',
        'ownership_type',
        'vendor_name',
        'commission_type',
        'commission_value',
        'insurance_provider',
        'policy_number',
        'insurance_expiry_date',
        'vehicle_image_path',
        'insurance_document_path',
        'rc_number',
        'rc_document_path',
        'last_service_date',
        'last_service_meter_reading',
        'next_service_due',
        'next_service_meter_reading',
        'remarks_notes',
        'is_available',
        'unavailable_from',
        'unavailable_until',
    ];

    protected $casts = [
        'vehicle_year' => 'integer',
        'mileage' => 'decimal:2',
        'seating_capacity' => 'integer',
        'engine_capacity_cc' => 'integer',
        'payload_capacity_tons' => 'decimal:2',
        'rental_price_24h' => 'decimal:2',
        'km_limit_per_booking' => 'integer',
        'extra_rental_price_per_hour' => 'decimal:2',
        'extra_price_per_km' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'insurance_expiry_date' => 'date',
        'last_service_date' => 'date',
        'last_service_meter_reading' => 'integer',
        'next_service_due' => 'date',
        'next_service_meter_reading' => 'integer',
        'is_available' => 'boolean',
        'unavailable_from' => 'date',
        'unavailable_until' => 'date',
    ];

    // Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->vehicle_status === 'active';
    }

    public function isUnderMaintenance(): bool
    {
        return $this->vehicle_status === 'under_maintenance';
    }

    public function isInactive(): bool
    {
        return $this->vehicle_status === 'inactive';
    }

    public function isAvailable(): bool
    {
        return $this->is_available && $this->isActive();
    }

    /**
     * Check if vehicle is available based on bookings (not just manual availability)
     */
    public function isAvailableForBooking(): bool
    {
        // First check if vehicle is manually available and active
        if (!$this->is_available || !$this->isActive()) {
            return false;
        }

        // Check if there are any current or upcoming bookings
        $hasActiveBookings = $this->bookings()
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->where('end_date_time', '>=', now())
            ->exists();

        return !$hasActiveBookings;
    }

    /**
     * Get availability status based on bookings
     */
    public function getAvailabilityStatusAttribute(): string
    {
        if (!$this->isActive()) {
            return 'Inactive';
        }

        if (!$this->is_available) {
            return 'Unavailable';
        }

        // Check for active bookings
        $hasActiveBookings = $this->bookings()
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->where('end_date_time', '>=', now())
            ->exists();

        return $hasActiveBookings ? 'Unavailable' : 'Available';
    }

    /**
     * Get availability status badge class
     */
    public function getAvailabilityStatusBadgeClassAttribute(): string
    {
        $status = $this->availability_status;
        
        return match($status) {
            'Available' => 'success',
            'Unavailable' => 'warning',
            'Inactive' => 'secondary',
            default => 'secondary'
        };
    }

    public function isOwned(): bool
    {
        return $this->ownership_type === 'owned';
    }

    /**
     * Get the bookings for the vehicle.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the images for the vehicle.
     */
    public function images()
    {
        return $this->hasMany(VehicleImage::class)->ordered();
    }

    /**
     * Get the primary image for the vehicle.
     */
    public function primaryImage()
    {
        return $this->hasOne(VehicleImage::class)->where('is_primary', true);
    }

    /**
     * Get the first image for the vehicle (fallback if no primary).
     */
    public function firstImage()
    {
        return $this->hasOne(VehicleImage::class)->ordered();
    }

    public function isLeased(): bool
    {
        return $this->ownership_type === 'leased';
    }

    public function isVendorProvided(): bool
    {
        return $this->ownership_type === 'vendor_provided';
    }

    public function getVehicleTypeDisplayAttribute(): string
    {
        return match($this->vehicle_type) {
            'car' => 'Car',
            'bike_scooter' => 'Bike/Scooter',
            'heavy_vehicle' => 'Heavy Vehicle',
            default => 'Unknown'
        };
    }

    public function getTransmissionTypeDisplayAttribute(): string
    {
        return match($this->transmission_type) {
            'manual' => 'Manual',
            'automatic' => 'Automatic',
            'gear' => 'Gear',
            'gearless' => 'Gearless',
            default => 'N/A'
        };
    }

    public function getFuelTypeDisplayAttribute(): string
    {
        return ucfirst($this->fuel_type);
    }

    public function getOwnershipTypeDisplayAttribute(): string
    {
        return match($this->ownership_type) {
            'owned' => 'Owned',
            'leased' => 'Leased',
            'vendor_provided' => 'Vendor Provided',
            default => 'Unknown'
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->vehicle_status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'under_maintenance' => 'Under Maintenance',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->vehicle_status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'under_maintenance' => 'warning',
            default => 'secondary'
        };
    }

    public function getRentalInfoAttribute(): array
    {
        return [
            'base_price' => $this->rental_price_24h,
            'km_limit' => $this->km_limit_per_booking,
            'extra_per_hour' => $this->extra_rental_price_per_hour,
            'extra_per_km' => $this->extra_price_per_km,
        ];
    }

    public function getCommissionInfoAttribute(): array
    {
        if (!$this->isVendorProvided()) {
            return [];
        }

        return [
            'type' => $this->commission_type,
            'value' => $this->commission_value,
            'vendor' => $this->vendor_name,
        ];
    }

    public function getMaintenanceInfoAttribute(): array
    {
        return [
            'last_service_date' => $this->last_service_date,
            'last_service_meter' => $this->last_service_meter_reading,
            'next_service_due' => $this->next_service_due,
            'next_service_meter' => $this->next_service_meter_reading,
        ];
    }

    public function getInsuranceInfoAttribute(): array
    {
        return [
            'provider' => $this->insurance_provider,
            'policy_number' => $this->policy_number,
            'expiry_date' => $this->insurance_expiry_date,
            'document_path' => $this->insurance_document_path,
        ];
    }

    public function getRcInfoAttribute(): array
    {
        return [
            'rc_number' => $this->rc_number,
            'document_path' => $this->rc_document_path,
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('vehicle_status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('vehicle_status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    public function scopeByMake($query, $make)
    {
        return $query->where('vehicle_make', $make);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('vehicle_status', $status);
    }
}
