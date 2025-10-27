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

        foreach ($request->business_ids as $businessId) {
            Notification::create([
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
            $sentCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Notifications sent successfully to {$sentCount} business(es)."
        ]);
    }

    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|string|in:service_reminder,insurance_renewal,booking_reminder,maintenance,inspection,general',
            'priority' => 'required|string|in:low,medium,high',
            'due_date' => 'required|date|after:now'
        ]);

        $businesses = Business::where('status', 'active')->get();
        $sentCount = 0;

        foreach ($businesses as $business) {
            Notification::create([
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
            $sentCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Notifications sent successfully to all {$sentCount} active businesses."
        ]);
    }
}

