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
                $query->where('snooze_until', '>', Carbon::now('Asia/Kolkata'));
            } else {
                $query->whereNull('snooze_until');
            }
        }

        $notifications = $query->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->paginate(20);

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

            // Debug: Log the request data
            \Log::info('Snooze request data:', $request->all());

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

            // Debug: Log the snooze until value
            \Log::info('Snooze until:', ['snooze_until' => $snoozeUntil]);

            $updateData = [
                'snooze_until' => $snoozeUntil,
                'snoozed_at' => Carbon::now('Asia/Kolkata'),
                'snoozed_by' => $businessAdmin->id
            ];

            // Debug: Log the update data
            \Log::info('Update data:', $updateData);

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
}
