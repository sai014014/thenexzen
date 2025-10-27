<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        $query = Notification::where('business_id', $business->id)
            ->where('is_active', true);
        
        // Debug: Log the query to see what's being selected
        \Log::info('Notifications query - Business ID: ' . $business->id);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by due date
        if ($request->filled('due_date')) {
            switch ($request->due_date) {
                case 'today':
                    $query->whereDate('due_date', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('due_date', Carbon::tomorrow());
                    break;
                case 'this_week':
                    $query->whereBetween('due_date', [Carbon::now('Asia/Kolkata'), Carbon::now('Asia/Kolkata')->addWeek()]);
                    break;
                case 'overdue':
                    $query->where('due_date', '<', Carbon::now('Asia/Kolkata'));
                    break;
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'snoozed') {
                // Show only snoozed notifications
                $query->whereNotNull('snooze_until')
                      ->where('snooze_until', '>', Carbon::now('Asia/Kolkata'));
            } elseif ($request->status === 'pending') {
                // Show only non-snoozed, non-completed notifications
                $query->where(function($q) {
                    $q->whereNull('snooze_until')
                      ->orWhere('snooze_until', '<=', Carbon::now('Asia/Kolkata'));
                })->where('is_completed', false);
            } elseif ($request->status === 'completed') {
                // Show only completed notifications
                $query->where('is_completed', true);
            }
            // If status is empty or 'active', show everything (no filter)
        } else {
            // By default, show all notifications including snoozed ones
            // No additional filter needed
        }

        $notifications = $query->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->paginate(20);
        
        // Debug: Log the count and check snoozed notifications
        \Log::info('Notifications found: ' . $notifications->total());
        \Log::info('Current time: ' . Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s'));
        foreach($notifications as $notif) {
            \Log::info('Notification ID: ' . $notif->id . ', Title: ' . $notif->title . ', Snooze Until: ' . ($notif->snooze_until ? $notif->snooze_until->format('Y-m-d H:i:s') : 'NULL') . ', Status: ' . $notif->status);
        }

        return view('business.notifications.index', compact('notifications', 'business', 'businessAdmin'));
    }

    public function show(Notification $notification)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the notification belongs to this business
        if ($notification->business_id !== $business->id) {
            abort(403, 'Unauthorized access');
        }

        return view('business.notifications.show', compact('notification', 'business', 'businessAdmin'));
    }

    public function snooze(Request $request, Notification $notification)
    {
        try {
            $businessAdmin = Auth::guard('business_admin')->user();
            $business = $businessAdmin->business;

            // Ensure the notification belongs to this business
            if ($notification->business_id !== $business->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }

            $request->validate([
                'snooze_period' => 'required|in:1_hour,1_day,1_week,custom',
                'custom_date' => 'nullable|required_if:snooze_period,custom|date|after:now'
            ]);

            $snoozeUntil = null;

            switch ($request->snooze_period) {
                case '1_hour':
                    $snoozeUntil = Carbon::now('Asia/Kolkata')->addHour();
                    break;
                case '1_day':
                    $snoozeUntil = Carbon::now('Asia/Kolkata')->addDay();
                    break;
                case '1_week':
                    $snoozeUntil = Carbon::now('Asia/Kolkata')->addWeek();
                    break;
                case 'custom':
                    if ($request->custom_date) {
                        $snoozeUntil = Carbon::parse($request->custom_date);
                    } else {
                        throw new \Exception('Custom date is required when snooze period is custom');
                    }
                    break;
            }

            $updateData = [
                'snooze_until' => $snoozeUntil,
                'snoozed_at' => Carbon::now('Asia/Kolkata'),
                'snoozed_by' => $businessAdmin->id
            ];

            $notification->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Notification snoozed successfully',
                'snooze_until' => $snoozeUntil->format('M d, Y H:i')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in snooze:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_merge(...array_values($e->errors())))
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in snooze method:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while snoozing the notification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markCompleted(Request $request, Notification $notification)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the notification belongs to this business
        if ($notification->business_id !== $business->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'completion_notes' => 'nullable|string|max:1000'
        ]);

        $notification->update([
            'is_completed' => true,
            'completed_at' => Carbon::now('Asia/Kolkata'),
            'completed_by' => $businessAdmin->id,
            'completion_notes' => $request->completion_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as completed'
        ]);
    }

    public function delete(Notification $notification)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the notification belongs to this business
        if ($notification->business_id !== $business->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    public function getNotificationCount()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        $count = Notification::where('business_id', $business->id)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('snooze_until')
                      ->orWhere('snooze_until', '<=', Carbon::now('Asia/Kolkata'));
            })
            ->where('is_completed', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getDashboardNotifications()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        $now = Carbon::now('Asia/Kolkata');

        // Get all active notifications that are not snoozed
        $allNotifications = Notification::where('business_id', $business->id)
            ->where('is_active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('snooze_until')
                      ->orWhere('snooze_until', '<=', $now);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unread notifications (not completed)
        $unreadNotifications = $allNotifications->where('is_completed', false);
        
        // Format notifications for the frontend
        $formattedNotifications = $allNotifications->map(function($notification) {
            return [
                'notificationId' => $notification->id,
                'bookingId' => $notification->booking_id ?? 0,
                'vehicleId' => $notification->vehicle_id ?? 0,
                'notificationHeading' => $notification->title,
                'notificationDetail' => $notification->message ?? $notification->description ?? $notification->title,
                'lastSnoozeTime' => $notification->created_at->format('Y-m-d H:i:s'),
                'isRead' => $notification->is_completed ? 1 : 0,
                'snoozeUntil' => $notification->snooze_until ? $notification->snooze_until->format('Y-m-d H:i:s') : null,
                'isSnoozed' => $notification->is_snoozed ? 1 : 0
            ];
        });

        return response()->json([
            'allNotifications' => $formattedNotifications,
            'unread_notifications' => $unreadNotifications->map(function($notification) {
                return [
                    'notificationId' => $notification->id,
                    'bookingId' => $notification->booking_id ?? 0,
                    'vehicleId' => $notification->vehicle_id ?? 0,
                    'notificationHeading' => $notification->title,
                    'notificationDetail' => $notification->message ?? $notification->description ?? $notification->title,
                    'lastSnoozeTime' => $notification->created_at->format('Y-m-d H:i:s'),
                    'isRead' => 0
                ];
            })->values(),
            'total_unread_count' => $unreadNotifications->count()
        ]);
    }
}
