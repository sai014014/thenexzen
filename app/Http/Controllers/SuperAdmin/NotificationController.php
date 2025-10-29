<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with(['business', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super-admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $businesses = Business::where('status', 'active')->orderBy('business_name')->get();
        
        return view('super-admin.notifications.create', compact('businesses'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'business_id' => 'required|exists:businesses,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'category' => 'required|string|in:service_reminder,insurance_renewal,booking_reminder,maintenance,inspection,general',
                'priority' => 'required|string|in:low,medium,high',
                'due_date' => 'required|date|after:now',
                'vehicle_id' => 'nullable|exists:vehicles,id'
            ]);

            $notification = Notification::create([
                'business_id' => $request->business_id,
                'vehicle_id' => $request->vehicle_id,
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'due_date' => Carbon::parse($request->due_date),
                'is_active' => true,
                'is_completed' => false,
                'metadata' => [
                    'created_by' => 'super_admin',
                    'created_by_user' => Auth::guard('super_admin')->user()->name ?? 'Super Admin',
                    'created_at' => now()
                ]
            ]);

            return redirect()->route('super-admin.notifications.index')
                ->with('success', 'Notification sent successfully to business.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in store notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'An error occurred while sending the notification: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Notification $notification)
    {
        $notification->load(['business', 'vehicle']);
        return view('super-admin.notifications.show', compact('notification'));
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        
        return redirect()->route('super-admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    public function bulkSend(Request $request)
    {
        try {
            $request->validate([
                'business_ids' => 'required|array',
                'business_ids.*' => 'exists:businesses,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'category' => 'required|string|in:service_reminder,insurance_renewal,booking_reminder,maintenance,inspection,general',
                'priority' => 'required|string|in:low,medium,high',
                'due_date' => 'required|date|after:now'
            ]);

            $sentCount = 0;
            $errors = [];

            foreach ($request->business_ids as $businessId) {
                try {
                    // Verify business exists
                    $business = Business::find($businessId);
                    if (!$business) {
                        throw new \Exception("Business with ID {$businessId} not found");
                    }

                    \Log::info('Attempting to create notification', [
                        'business_id' => $businessId,
                        'title' => $request->title,
                        'category' => $request->category
                    ]);

                    $notification = Notification::create([
                        'business_id' => $businessId,
                        'title' => $request->title,
                        'description' => $request->description,
                        'category' => $request->category,
                        'priority' => $request->priority,
                        'due_date' => Carbon::parse($request->due_date),
                        'is_active' => true,
                        'is_completed' => false,
                        'metadata' => [
                            'created_by' => 'super_admin',
                            'created_by_user' => Auth::guard('super_admin')->user()->name ?? 'Super Admin',
                            'created_at' => now(),
                            'bulk_sent' => true
                        ]
                    ]);

                    \Log::info('Notification created successfully', [
                        'notification_id' => $notification->id,
                        'business_id' => $businessId
                    ]);

                    $sentCount++;
                } catch (\Exception $e) {
                    \Log::error('Failed to create notification for business', [
                        'business_id' => $businessId,
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $errors[] = "Failed to send notification to business ID {$businessId}: " . $e->getMessage();
                }
            }

            if ($sentCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notifications to any business.',
                    'errors' => $errors
                ], 500);
            }

            $message = "Notifications sent successfully to {$sentCount} business(es).";
            if (count($errors) > 0) {
                $message .= " However, " . count($errors) . " notification(s) failed to send.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'sent_count' => $sentCount,
                'failed_count' => count($errors),
                'errors' => $errors
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in bulkSend notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendToAll(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'category' => 'required|string|in:service_reminder,insurance_renewal,booking_reminder,maintenance,inspection,general',
                'priority' => 'required|string|in:low,medium,high',
                'due_date' => 'required|date|after:now'
            ]);

            $businesses = Business::where('status', 'active')->get();
            $sentCount = 0;
            $errors = [];

            foreach ($businesses as $business) {
                try {
                    \Log::info('Attempting to create notification for business', [
                        'business_id' => $business->id,
                        'business_name' => $business->business_name,
                        'title' => $request->title,
                        'category' => $request->category
                    ]);

                    $notification = Notification::create([
                        'business_id' => $business->id,
                        'title' => $request->title,
                        'description' => $request->description,
                        'category' => $request->category,
                        'priority' => $request->priority,
                        'due_date' => Carbon::parse($request->due_date),
                        'is_active' => true,
                        'is_completed' => false,
                        'metadata' => [
                            'created_by' => 'super_admin',
                            'created_by_user' => Auth::guard('super_admin')->user()->name ?? 'Super Admin',
                            'created_at' => now(),
                            'sent_to_all' => true
                        ]
                    ]);

                    \Log::info('Notification created successfully', [
                        'notification_id' => $notification->id,
                        'business_id' => $business->id
                    ]);

                    $sentCount++;
                } catch (\Exception $e) {
                    \Log::error('Failed to create notification for business', [
                        'business_id' => $business->id,
                        'business_name' => $business->business_name,
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $errors[] = "Failed to send notification to {$business->business_name} (ID: {$business->id}): " . $e->getMessage();
                }
            }

            if ($sentCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notifications to any business.',
                    'errors' => $errors
                ], 500);
            }

            $message = "Notifications sent successfully to {$sentCount} active business(es).";
            if (count($errors) > 0) {
                $message .= " However, " . count($errors) . " notification(s) failed to send.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'sent_count' => $sentCount,
                'failed_count' => count($errors),
                'errors' => $errors
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in sendToAll notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending notifications: ' . $e->getMessage()
            ], 500);
        }
    }
}

