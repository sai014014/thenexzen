<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'customer_id',
        'vehicle_id',
        'booking_number',
        'start_date_time',
        'end_date_time',
        'status',
        'base_rental_price',
        'extra_charges',
        'total_amount',
        'amount_paid',
        'amount_due',
        'advance_amount',
        'payment_method',
        'advance_payment_method',
        'notes',
        'customer_notes',
        'cancellation_reason',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'base_rental_price' => 'decimal:2',
        'extra_charges' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'advance_amount' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'upcoming',
        'extra_charges' => 0,
        'amount_paid' => 0,
        'advance_amount' => 0,
    ];

    /**
     * Get the business that owns the booking.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the customer for the booking.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the vehicle for the booking.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Generate a unique booking number.
     */
    public static function generateBookingNumber(): string
    {
        do {
            $bookingNumber = 'BK' . date('Ymd') . rand(1000, 9999);
        } while (static::where('booking_number', $bookingNumber)->exists());
        
        return $bookingNumber;
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'upcoming' => 'Upcoming',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'upcoming' => 'bg-warning',
            'ongoing' => 'bg-primary',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the payment method label.
     */
    public function getPaymentMethodLabelAttribute(): ?string
    {
        if (!$this->payment_method) {
            return null;
        }
        
        return match($this->payment_method) {
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'upi' => 'UPI',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            default => 'Unknown'
        };
    }

    /**
     * Get the advance payment method label.
     */
    public function getAdvancePaymentMethodLabelAttribute(): ?string
    {
        if (!$this->advance_payment_method) {
            return null;
        }
        
        return match($this->advance_payment_method) {
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'upi' => 'UPI',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            default => 'Unknown'
        };
    }

    /**
     * Get the rental duration in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return $this->start_date_time->diffInHours($this->end_date_time);
    }

    /**
     * Get the rental duration in days.
     */
    public function getDurationInDaysAttribute(): float
    {
        return $this->start_date_time->diffInDays($this->end_date_time);
    }

    /**
     * Get the formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = $this->duration_in_hours;
        $days = floor($hours / 24);
        $remainingHours = $hours % 24;
        
        if ($days > 0) {
            return $days . ' day' . ($days > 1 ? 's' : '') . 
                   ($remainingHours > 0 ? ' ' . $remainingHours . ' hour' . ($remainingHours > 1 ? 's' : '') : '');
        }
        
        return $hours . ' hour' . ($hours > 1 ? 's' : '');
    }

    /**
     * Check if the booking is currently active (ongoing).
     */
    public function isActive(): bool
    {
        return $this->status === 'ongoing' && 
               $this->started_at && 
               !$this->completed_at;
    }

    /**
     * Check if the booking is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming' && 
               $this->start_date_time > now();
    }

    /**
     * Check if the booking is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' && 
               $this->completed_at;
    }

    /**
     * Check if the booking is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled' && 
               $this->cancelled_at;
    }

    /**
     * Check if the booking is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'ongoing' && 
               $this->end_date_time < now();
    }

    /**
     * Calculate the total amount based on duration and vehicle pricing.
     */
    public function calculateTotalAmount(): float
    {
        $hours = $this->duration_in_hours;
        $basePrice = $this->base_rental_price;
        
        // Calculate based on 24-hour periods
        $days = ceil($hours / 24);
        $totalAmount = $basePrice * $days;
        
        // Add extra charges
        $totalAmount += $this->extra_charges;
        
        return $totalAmount;
    }

    /**
     * Update the amount due.
     */
    public function updateAmountDue(): void
    {
        $this->amount_due = $this->total_amount - $this->amount_paid;
        $this->save();
    }

    /**
     * Start the booking.
     */
    public function start(): bool
    {
        if ($this->status !== 'upcoming') {
            return false;
        }
        
        $this->update([
            'status' => 'ongoing',
            'started_at' => now(),
        ]);
        
        // Mark vehicle as unavailable when booking starts
        $this->vehicle->update([
            'is_available' => false,
            'unavailable_from' => $this->start_date_time,
            'unavailable_until' => $this->end_date_time,
        ]);
        
        return true;
    }

    /**
     * Complete the booking.
     */
    public function complete(): bool
    {
        if ($this->status !== 'ongoing') {
            return false;
        }
        
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Mark vehicle as available when booking completes
        $this->vehicle->update([
            'is_available' => true,
            'unavailable_from' => null,
            'unavailable_until' => null,
        ]);
        
        return true;
    }

    /**
     * Cancel the booking.
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }
        
        $wasOngoing = $this->status === 'ongoing';
        
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
        
        // If booking was ongoing, mark vehicle as available when cancelled
        if ($wasOngoing) {
            $this->vehicle->update([
                'is_available' => true,
                'unavailable_from' => null,
                'unavailable_until' => null,
            ]);
        }
        
        return true;
    }

    /**
     * Check if vehicle is available for the given time period.
     */
    public static function isVehicleAvailable(int $vehicleId, Carbon $startDateTime, Carbon $endDateTime, ?int $excludeBookingId = null): bool
    {
        // First check if the vehicle exists
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle) {
            return false;
        }
        
        // Check if vehicle is generally available
        if (!$vehicle->is_available) {
            // If vehicle is marked as unavailable, check if the requested period is outside the unavailable period
            if ($vehicle->unavailable_from && $vehicle->unavailable_until) {
                $unavailableStart = Carbon::parse($vehicle->unavailable_from);
                $unavailableEnd = Carbon::parse($vehicle->unavailable_until);
                
                // Check if the requested period overlaps with the unavailable period
                if ($unavailableStart->lte($endDateTime) && $unavailableEnd->gte($startDateTime)) {
                    return false; // Requested period overlaps with unavailable period
                }
                // If no overlap, vehicle should be available for this period
            } else {
                return false; // Vehicle is marked as unavailable with no specific dates
            }
        }
        
        // Check for booking conflicts (only ongoing and upcoming bookings)
        $query = static::where('vehicle_id', $vehicleId)
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                // Check for any overlap in time periods
                $q->where(function ($overlapQuery) use ($startDateTime, $endDateTime) {
                    // New booking starts during existing booking
                    $overlapQuery->where('start_date_time', '<=', $startDateTime)
                                ->where('end_date_time', '>', $startDateTime)
                    // OR new booking ends during existing booking
                    ->orWhere('start_date_time', '<', $endDateTime)
                                ->where('end_date_time', '>=', $endDateTime)
                    // OR new booking completely contains existing booking
                    ->orWhere(function ($containQuery) use ($startDateTime, $endDateTime) {
                        $containQuery->where('start_date_time', '>=', $startDateTime)
                                    ->where('end_date_time', '<=', $endDateTime);
                    });
                });
            });
        
        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }
        
        return $query->count() === 0;
    }

    /**
     * Get available vehicles for a given time period.
     */
    public static function getAvailableVehicles(int $businessId, Carbon $startDateTime, Carbon $endDateTime, ?int $excludeBookingId = null): \Illuminate\Database\Eloquent\Collection
    {
        $bookedVehicleIds = static::where('business_id', $businessId)
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                // Check for any overlap in time periods
                $q->where(function ($overlapQuery) use ($startDateTime, $endDateTime) {
                    // New booking starts during existing booking
                    $overlapQuery->where('start_date_time', '<=', $startDateTime)
                                ->where('end_date_time', '>', $startDateTime)
                    // OR new booking ends during existing booking
                    ->orWhere('start_date_time', '<', $endDateTime)
                                ->where('end_date_time', '>=', $endDateTime)
                    // OR new booking completely contains existing booking
                    ->orWhere(function ($containQuery) use ($startDateTime, $endDateTime) {
                        $containQuery->where('start_date_time', '>=', $startDateTime)
                                    ->where('end_date_time', '<=', $endDateTime);
                    });
                });
            })
            ->pluck('vehicle_id');
        
        if ($excludeBookingId) {
            $bookedVehicleIds = $bookedVehicleIds->filter(function ($id) use ($excludeBookingId) {
                return $id !== $excludeBookingId;
            });
        }
        
        $vehicles = Vehicle::where('business_id', $businessId)
            ->whereNotIn('id', $bookedVehicleIds)
            ->get()
            ->filter(function ($vehicle) use ($startDateTime, $endDateTime) {
                // Check if vehicle is generally available
                if ($vehicle->is_available) {
                    return true;
                }
                
                // If vehicle is marked as unavailable, check if the requested period is outside the unavailable period
                if ($vehicle->unavailable_from && $vehicle->unavailable_until) {
                    $unavailableStart = Carbon::parse($vehicle->unavailable_from);
                    $unavailableEnd = Carbon::parse($vehicle->unavailable_until);
                    
                    // Check if the requested period overlaps with the unavailable period
                    if ($unavailableStart->lte($endDateTime) && $unavailableEnd->gte($startDateTime)) {
                        return false; // Requested period overlaps with unavailable period
                    }
                    // If no overlap, vehicle should be available for this period
                    return true;
                }
                
                return false; // Vehicle is marked as unavailable with no specific dates
            });
            
        return $vehicles->values();
    }
}