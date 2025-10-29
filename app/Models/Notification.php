<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'business_id',
        'vehicle_id',
        'booking_id',
        'title',
        'description',
        'message',
        'category',
        'priority',
        'due_date',
        'is_active',
        'is_completed',
        'completed_at',
        'completed_by',
        'completion_notes',
        'snooze_until',
        'snoozed_at',
        'snoozed_by',
        'metadata'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'snooze_until' => 'datetime',
        'snoozed_at' => 'datetime',
        'is_active' => 'boolean',
        'is_completed' => 'boolean',
        'metadata' => 'array'
    ];

    // Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(BusinessAdmin::class, 'completed_by');
    }

    public function snoozedBy(): BelongsTo
    {
        return $this->belongsTo(BusinessAdmin::class, 'snoozed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeSnoozed($query)
    {
        return $query->where('snooze_until', '>', Carbon::now('Asia/Kolkata'));
    }

    public function scopeNotSnoozed($query)
    {
        return $query->where(function($q) {
            $q->whereNull('snooze_until')
              ->orWhere('snooze_until', '<=', Carbon::now('Asia/Kolkata'));
        });
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now('Asia/Kolkata'));
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', Carbon::today());
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_date', [Carbon::now('Asia/Kolkata'), Carbon::now('Asia/Kolkata')->addWeek()]);
    }

    // Accessors & Mutators
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info',
            default => 'secondary'
        };
    }

    public function getPriorityIconAttribute()
    {
        return match($this->priority) {
            'high' => 'fas fa-exclamation-triangle',
            'medium' => 'fas fa-exclamation-circle',
            'low' => 'fas fa-info-circle',
            default => 'fas fa-bell'
        };
    }

    public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'service_reminder' => 'fas fa-wrench',
            'insurance_renewal' => 'fas fa-shield-alt',
            'booking_reminder' => 'fas fa-calendar-check',
            'maintenance' => 'fas fa-tools',
            'inspection' => 'fas fa-search',
            default => 'fas fa-bell'
        };
    }

    public function getStatusAttribute()
    {
        if ($this->is_completed) {
            return 'completed';
        }

        if ($this->snooze_until && $this->snooze_until > Carbon::now('Asia/Kolkata')) {
            return 'snoozed';
        }

        if ($this->due_date < Carbon::now('Asia/Kolkata')) {
            return 'overdue';
        }

        return 'pending';
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < Carbon::now('Asia/Kolkata') && !$this->is_completed;
    }

    public function getIsSnoozedAttribute()
    {
        return $this->snooze_until && $this->snooze_until > Carbon::now('Asia/Kolkata');
    }

    // Static methods for creating notifications
    public static function createServiceReminder(Vehicle $vehicle, Carbon $dueDate, $priority = 'medium')
    {
        $vehicleName = $vehicle->vehicle_make . ' ' . $vehicle->vehicle_model . ' (' . $vehicle->vehicle_number . ')';
        
        return self::create([
            'business_id' => $vehicle->business_id,
            'vehicle_id' => $vehicle->id,
            'title' => 'Service Reminder - ' . $vehicleName,
            'description' => "Vehicle {$vehicleName} is due for service on {$dueDate->format('M d, Y')}.",
            'category' => 'service_reminder',
            'priority' => $priority,
            'due_date' => $dueDate,
            'is_active' => true,
            'is_completed' => false,
            'metadata' => [
                'vehicle_make' => $vehicle->vehicle_make,
                'vehicle_model' => $vehicle->vehicle_model,
                'vehicle_number' => $vehicle->vehicle_number,
                'vehicle_id' => $vehicle->id,
                'service_type' => 'routine_service'
            ]
        ]);
    }

    public static function createInsuranceReminder(Vehicle $vehicle, Carbon $dueDate, $priority = 'high')
    {
        $vehicleName = $vehicle->vehicle_make . ' ' . $vehicle->vehicle_model . ' (' . $vehicle->vehicle_number . ')';
        
        return self::create([
            'business_id' => $vehicle->business_id,
            'vehicle_id' => $vehicle->id,
            'title' => 'Insurance Renewal - ' . $vehicleName,
            'description' => "Insurance for {$vehicleName} expires on {$dueDate->format('M d, Y')}. Please renew to avoid coverage lapse.",
            'category' => 'insurance_renewal',
            'priority' => $priority,
            'due_date' => $dueDate,
            'is_active' => true,
            'is_completed' => false,
            'metadata' => [
                'vehicle_make' => $vehicle->vehicle_make,
                'vehicle_model' => $vehicle->vehicle_model,
                'vehicle_number' => $vehicle->vehicle_number,
                'vehicle_id' => $vehicle->id,
                'insurance_type' => 'comprehensive'
            ]
        ]);
    }
}
