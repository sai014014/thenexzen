<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Generate service reminders for all vehicles
     */
    public function generateServiceReminders()
    {
        $vehicles = Vehicle::where('is_active', true)->get();
        
        foreach ($vehicles as $vehicle) {
            $this->checkServiceReminder($vehicle);
        }
        
        Log::info('Service reminders generated for ' . $vehicles->count() . ' vehicles');
    }

    /**
     * Generate insurance renewal reminders for all vehicles
     */
    public function generateInsuranceReminders()
    {
        $vehicles = Vehicle::where('is_active', true)->get();
        
        foreach ($vehicles as $vehicle) {
            $this->checkInsuranceReminder($vehicle);
        }
        
        Log::info('Insurance reminders generated for ' . $vehicles->count() . ' vehicles');
    }

    /**
     * Check and create service reminder for a vehicle
     */
    private function checkServiceReminder(Vehicle $vehicle)
    {
        // Check if vehicle has service interval set
        if (!$vehicle->service_interval_km && !$vehicle->service_interval_months) {
            return;
        }

        $lastServiceDate = $vehicle->last_service_date;
        $lastServiceKm = $vehicle->last_service_km;
        $currentKm = $vehicle->current_km;

        $shouldCreateReminder = false;
        $dueDate = null;
        $priority = 'medium';

        // Check by mileage
        if ($vehicle->service_interval_km && $lastServiceKm) {
            $kmSinceLastService = $currentKm - $lastServiceKm;
            if ($kmSinceLastService >= $vehicle->service_interval_km) {
                $shouldCreateReminder = true;
                $dueDate = Carbon::now('Asia/Kolkata')->addDays(7); // Give 7 days notice
                $priority = 'high';
            } elseif ($kmSinceLastService >= ($vehicle->service_interval_km * 0.9)) {
                $shouldCreateReminder = true;
                $dueDate = Carbon::now('Asia/Kolkata')->addDays(14); // Give 14 days notice
                $priority = 'medium';
            }
        }

        // Check by time
        if ($vehicle->service_interval_months && $lastServiceDate) {
            $monthsSinceLastService = $lastServiceDate->diffInMonths(Carbon::now('Asia/Kolkata'));
            if ($monthsSinceLastService >= $vehicle->service_interval_months) {
                $shouldCreateReminder = true;
                $dueDate = Carbon::now('Asia/Kolkata')->addDays(7);
                $priority = 'high';
            } elseif ($monthsSinceLastService >= ($vehicle->service_interval_months * 0.8)) {
                $shouldCreateReminder = true;
                $dueDate = Carbon::now('Asia/Kolkata')->addDays(14);
                $priority = 'medium';
            }
        }

        if ($shouldCreateReminder) {
            // Check if reminder already exists
            $existingReminder = Notification::where('business_id', $vehicle->business_id)
                ->where('vehicle_id', $vehicle->id)
                ->where('category', 'service_reminder')
                ->where('is_completed', false)
                ->where('is_active', true)
                ->first();

            if (!$existingReminder) {
                Notification::createServiceReminder($vehicle, $dueDate, $priority);
            }
        }
    }

    /**
     * Check and create insurance renewal reminder for a vehicle
     */
    private function checkInsuranceReminder(Vehicle $vehicle)
    {
        if (!$vehicle->insurance_expiry_date) {
            return;
        }

        $expiryDate = Carbon::parse($vehicle->insurance_expiry_date);
        $daysUntilExpiry = Carbon::now('Asia/Kolkata')->diffInDays($expiryDate, false);

        $shouldCreateReminder = false;
        $priority = 'medium';

        if ($daysUntilExpiry <= 0) {
            // Already expired
            $shouldCreateReminder = true;
            $priority = 'high';
        } elseif ($daysUntilExpiry <= 7) {
            // Expires within 7 days
            $shouldCreateReminder = true;
            $priority = 'high';
        } elseif ($daysUntilExpiry <= 30) {
            // Expires within 30 days
            $shouldCreateReminder = true;
            $priority = 'medium';
        } elseif ($daysUntilExpiry <= 60) {
            // Expires within 60 days
            $shouldCreateReminder = true;
            $priority = 'low';
        }

        if ($shouldCreateReminder) {
            // Check if reminder already exists
            $existingReminder = Notification::where('business_id', $vehicle->business_id)
                ->where('vehicle_id', $vehicle->id)
                ->where('category', 'insurance_renewal')
                ->where('is_completed', false)
                ->where('is_active', true)
                ->first();

            if (!$existingReminder) {
                Notification::createInsuranceReminder($vehicle, $expiryDate, $priority);
            }
        }
    }

    /**
     * Generate all notifications
     */
    public function generateAllNotifications()
    {
        $this->generateServiceReminders();
        $this->generateInsuranceReminders();
    }

    /**
     * Clean up old completed notifications
     */
    public function cleanupOldNotifications($daysOld = 30)
    {
        $cutoffDate = Carbon::now('Asia/Kolkata')->subDays($daysOld);
        
        $deletedCount = Notification::where('is_completed', true)
            ->where('completed_at', '<', $cutoffDate)
            ->delete();

        Log::info("Cleaned up {$deletedCount} old completed notifications");
        
        return $deletedCount;
    }

    /**
     * Update notification counts for header
     */
    public function getNotificationCounts($businessId)
    {
        $total = Notification::where('business_id', $businessId)
            ->where('is_active', true)
            ->where('is_completed', false)
            ->where(function($query) {
                $query->whereNull('snooze_until')
                      ->orWhere('snooze_until', '<=', Carbon::now('Asia/Kolkata'));
            })
            ->count();

        $overdue = Notification::where('business_id', $businessId)
            ->where('is_active', true)
            ->where('is_completed', false)
            ->where('due_date', '<', Carbon::now('Asia/Kolkata'))
            ->where(function($query) {
                $query->whereNull('snooze_until')
                      ->orWhere('snooze_until', '<=', Carbon::now('Asia/Kolkata'));
            })
            ->count();

        return [
            'total' => $total,
            'overdue' => $overdue
        ];
    }
}
