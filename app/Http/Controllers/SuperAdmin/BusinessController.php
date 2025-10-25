<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\BusinessAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\BusinessStatusChanged;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::with('businessAdmins')->latest()->paginate(10);
        return view('super-admin.businesses.index', compact('businesses'));
    }

    public function create()
    {
        return view('super-admin.businesses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|in:car_dealership,car_rental,car_service,car_insurance',
            'description' => 'nullable|string',
            'email' => 'required|email|unique:businesses,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'website' => 'nullable|url',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:business_admins,email',
            'admin_password' => 'required|string|min:8',
            'admin_phone' => 'nullable|string|max:20',
        ]);

        // Create business
        $business = Business::create([
            'business_name' => $request->business_name,
            'business_slug' => Str::slug($request->business_name),
            'business_type' => $request->business_type,
            'description' => $request->description,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'website' => $request->website,
            'status' => 'active',
            'subscription_plan' => 'basic',
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // Create business admin
        BusinessAdmin::create([
            'business_id' => $business->id,
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'phone' => $request->admin_phone,
            'role' => 'admin',
            'permissions' => ['all'], // Give all permissions to admin
            'is_active' => true,
        ]);

        return redirect()->route('super-admin.businesses.index')
            ->with('success', 'Business created successfully!');
    }

    public function show(Business $business)
    {
        $business->load(['businessAdmins', 'subscriptions.subscriptionPackage']);
        
        // Get active subscription
        $activeSubscription = $business->subscriptions()
            ->with('subscriptionPackage')
            ->whereIn('status', ['active', 'trial'])
            ->first();
            
        return view('super-admin.businesses.show', compact('business', 'activeSubscription'));
    }

    public function edit(Business $business)
    {
        return view('super-admin.businesses.edit', compact('business'));
    }

    public function update(Request $request, Business $business)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|in:car_dealership,car_rental,car_service,car_insurance',
            'description' => 'nullable|string',
            'email' => 'required|email|unique:businesses,email,' . $business->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'website' => 'nullable|url',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $business->update([
            'business_name' => $request->business_name,
            'business_slug' => Str::slug($request->business_name),
            'business_type' => $request->business_type,
            'description' => $request->description,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'website' => $request->website,
            'status' => $request->status,
        ]);

        return redirect()->route('super-admin.businesses.index')
            ->with('success', 'Business updated successfully!');
    }

    public function destroy(Business $business)
    {
        $business->delete();
        return redirect()->route('super-admin.businesses.index')
            ->with('success', 'Business deleted successfully!');
    }

    public function updateStatus(Request $request, Business $business)
    {
        try {
            \Log::info('Status update request received', [
                'business_id' => $business->id,
                'request_data' => $request->all(),
                'is_ajax' => $request->ajax()
            ]);

            $request->validate([
                'status' => 'required|in:active,inactive,suspended'
            ]);

            $oldStatus = $business->status;
            $newStatus = $request->status;

            $business->update([
                'status' => $newStatus
            ]);

            // Send notification to all business admins
            if ($oldStatus !== $newStatus) {
                $businessAdmins = $business->businessAdmins;
                foreach ($businessAdmins as $admin) {
                    $admin->notify(new BusinessStatusChanged($oldStatus, $newStatus));
                }
            }

            \Log::info('Status updated successfully', [
                'business_id' => $business->id,
                'old_status' => $oldStatus,
                'new_status' => $business->status
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Business status updated successfully!',
                    'status' => $business->status,
                    'status_badge' => $this->getStatusBadge($business->status)
                ]);
            }

            return redirect()->route('super-admin.businesses.index')
                ->with('success', 'Business status updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Business status update error: ' . $e->getMessage(), [
                'business_id' => $business->id ?? 'unknown',
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('super-admin.businesses.index')
                ->with('error', 'An error occurred while updating status.');
        }
    }

    private function getStatusBadge($status)
    {
        switch ($status) {
            case 'active':
                return '<span class="badge bg-success">Active</span>';
            case 'inactive':
                return '<span class="badge bg-secondary">Inactive</span>';
            case 'suspended':
                return '<span class="badge bg-danger">Suspended</span>';
            default:
                return '<span class="badge bg-secondary">Unknown</span>';
        }
    }
}
