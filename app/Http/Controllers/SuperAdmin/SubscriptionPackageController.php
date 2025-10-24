<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Models\BusinessSubscription;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SubscriptionPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionPackage::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('package_name', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'package_name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['package_name', 'subscription_fee', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $packages = $query->withCount('activeBusinessSubscriptions')->paginate(15);

        return view('super-admin.subscription-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('super-admin.subscription-packages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required|string|max:255|unique:subscription_packages,package_name',
            'subscription_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'trial_period_days' => 'required|integer|min:0|max:365',
            'onboarding_fee' => 'required|numeric|min:0',
            'vehicle_capacity' => 'nullable|integer|min:1',
            'is_unlimited_vehicles' => 'boolean',
            'status' => 'required|in:active,inactive,draft',
            'description' => 'nullable|string',
            'features_summary' => 'nullable|string',
            'enabled_modules' => 'nullable|array',
            'enabled_modules.*' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle unlimited vehicles
        if ($request->is_unlimited_vehicles) {
            $data['vehicle_capacity'] = null;
        }

        // Set default values for fields not in the form but required by database
        $data['billing_cycles'] = ['monthly']; // Default billing cycle
        $data['payment_methods'] = ['direct_debit']; // Default payment method
        $data['renewal_type'] = 'auto_renew'; // Default renewal type
        $data['support_type'] = 'standard'; // Default support type
        $data['show_on_website'] = true; // Default visibility
        $data['internal_use_only'] = false; // Default internal use

        $package = SubscriptionPackage::create($data);

        // Log package creation
        Log::info('Subscription package created', [
            'package_id' => $package->id,
            'package_name' => $package->package_name,
            'enabled_modules' => $package->enabled_modules
        ]);

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package created successfully!');
    }

    public function show(SubscriptionPackage $subscriptionPackage)
    {
        // Load active business subscriptions with business details
        $subscriptionPackage->load(['activeBusinessSubscriptions.business']);
        
        return view('super-admin.subscription-packages.show', compact('subscriptionPackage'));
    }

    public function edit(SubscriptionPackage $subscriptionPackage)
    {
        // Load business count for warning display
        $subscriptionPackage->loadCount('activeBusinessSubscriptions');
        
        return view('super-admin.subscription-packages.edit', compact('subscriptionPackage'));
    }

    public function update(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required|string|max:255|unique:subscription_packages,package_name,' . $subscriptionPackage->id,
            'subscription_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'trial_period_days' => 'required|integer|min:0|max:365',
            'onboarding_fee' => 'required|numeric|min:0',
            'vehicle_capacity' => 'nullable|integer|min:1',
            'is_unlimited_vehicles' => 'boolean',
            'status' => 'required|in:active,inactive,draft',
            'description' => 'nullable|string',
            'features_summary' => 'nullable|string',
            'enabled_modules' => 'nullable|array',
            'enabled_modules.*' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Check if trying to deactivate a package with active subscriptions
        if ($request->status === 'inactive' && $subscriptionPackage->status !== 'inactive') {
            if (!$subscriptionPackage->canBeDeactivated()) {
                $activeCount = $subscriptionPackage->active_business_count;
                return redirect()->back()
                    ->withErrors(['status' => "Cannot deactivate this package. It is currently being used by {$activeCount} active business(es). Please contact the businesses to upgrade or cancel their subscriptions first."])
                    ->withInput();
            }
        }
        
        // Handle unlimited vehicles
        if ($request->is_unlimited_vehicles) {
            $data['vehicle_capacity'] = null;
        }

        // Set default values for fields not in the form but required by database
        $data['billing_cycles'] = $subscriptionPackage->billing_cycles ?? ['monthly'];
        $data['payment_methods'] = $subscriptionPackage->payment_methods ?? ['direct_debit'];
        $data['renewal_type'] = $subscriptionPackage->renewal_type ?? 'auto_renew';
        $data['support_type'] = $subscriptionPackage->support_type ?? 'standard';
        $data['show_on_website'] = $subscriptionPackage->show_on_website ?? true;
        $data['internal_use_only'] = $subscriptionPackage->internal_use_only ?? false;

        // Store original values to detect changes
        $originalModules = $subscriptionPackage->enabled_modules ?? [];

        $subscriptionPackage->update($data);

        // Check if modules changed
        $newModules = $subscriptionPackage->fresh()->enabled_modules ?? [];
        $modulesChanged = $originalModules !== $newModules;

        // If modules changed, update all business subscriptions
        if ($modulesChanged) {
            $this->updateBusinessSubscriptions($subscriptionPackage);
            
            Log::info('Subscription package updated - business subscriptions synced', [
                'package_id' => $subscriptionPackage->id,
                'package_name' => $subscriptionPackage->package_name,
                'modules_changed' => $modulesChanged,
                'old_modules' => $originalModules,
                'new_modules' => $newModules
            ]);
        }

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package updated successfully!');
    }

    public function destroy(SubscriptionPackage $subscriptionPackage)
    {
        $subscriptionPackage->delete();

        return redirect()->route('super-admin.subscription-packages.index')
            ->with('success', 'Subscription package deleted successfully!');
    }

    public function toggleStatus(SubscriptionPackage $subscriptionPackage)
    {
        $newStatus = $subscriptionPackage->status === 'active' ? 'inactive' : 'active';
        
        // Check if trying to deactivate a package with active subscriptions
        if ($newStatus === 'inactive') {
            if (!$subscriptionPackage->canBeDeactivated()) {
                $activeCount = $subscriptionPackage->active_business_count;
                return response()->json([
                    'success' => false,
                    'message' => "Cannot deactivate this package. It is currently being used by {$activeCount} active business(es). Please contact the businesses to upgrade or cancel their subscriptions first."
                ], 422);
            }
        }
        
        $subscriptionPackage->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Package status updated successfully!',
            'new_status' => $newStatus
        ]);
    }

    /**
     * Update all business subscriptions when package changes
     */
    private function updateBusinessSubscriptions(SubscriptionPackage $package)
    {
        try {
            // Get all active business subscriptions for this package
            $businessSubscriptions = BusinessSubscription::where('subscription_package_id', $package->id)
                ->whereIn('status', ['active', 'trial'])
                ->with('business')
                ->get();

            $updatedCount = 0;

            foreach ($businessSubscriptions as $subscription) {
                // Update module access based on package changes
                $subscription->update([
                    'module_access' => $package->enabled_modules ?? []
                ]);

                // Create notification for the business about package changes
                $this->createPackageChangeNotification($subscription, $package);

                $updatedCount++;

                // Log the update for each business
                Log::info('Business subscription updated due to package change', [
                    'business_id' => $subscription->business_id,
                    'business_name' => $subscription->business->business_name ?? 'Unknown',
                    'subscription_id' => $subscription->id,
                    'package_id' => $package->id,
                    'package_name' => $package->package_name,
                    'new_modules' => $package->enabled_modules
                ]);
            }

            Log::info('Business subscriptions updated', [
                'package_id' => $package->id,
                'package_name' => $package->package_name,
                'updated_subscriptions' => $updatedCount
            ]);

            return $updatedCount;

        } catch (\Exception $e) {
            Log::error('Failed to update business subscriptions', [
                'package_id' => $package->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 0;
        }
    }

    /**
     * Create notification for business about package changes
     */
    private function createPackageChangeNotification(BusinessSubscription $subscription, SubscriptionPackage $package)
    {
        try {
            $modules = $package->enabled_modules ?? [];
            $moduleNames = [];
            
            $moduleDisplayNames = [
                'vehicles' => 'Vehicle Management',
                'bookings' => 'Booking Management', 
                'customers' => 'Customer Management',
                'vendors' => 'Vendor Management',
                'reports' => 'Reports & Analytics',
                'notifications' => 'Notifications',
                'subscription' => 'Subscription Management'
            ];

            foreach ($modules as $module) {
                $moduleNames[] = $moduleDisplayNames[$module] ?? ucfirst(str_replace('_', ' ', $module));
            }

            Notification::create([
                'business_id' => $subscription->business_id,
                'title' => 'Subscription Package Updated',
                'description' => 'Your subscription package "' . $package->package_name . '" has been updated. Available modules: ' . implode(', ', $moduleNames),
                'category' => 'general',
                'priority' => 'medium',
                'due_date' => now()->addDays(7),
                'is_active' => true,
                'is_completed' => false,
                'metadata' => json_encode([
                    'subscription_id' => $subscription->id,
                    'package_id' => $package->id,
                    'package_name' => $package->package_name,
                    'enabled_modules' => $package->enabled_modules
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create package change notification', [
                'business_id' => $subscription->business_id,
                'package_id' => $package->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Add business to subscription package
     */
    public function addBusiness(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'trial_days' => 'required|integer|min:1|max:365'
        ]);

        $business = \App\Models\Business::findOrFail($request->business_id);

        // Check if business already has an active subscription
        $existingSubscription = \App\Models\BusinessSubscription::where('business_id', $business->id)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if ($existingSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'Business already has an active subscription. Please cancel their current subscription first.'
            ], 400);
        }

        // Create new subscription
        $subscription = \App\Models\BusinessSubscription::create([
            'business_id' => $business->id,
            'subscription_package_id' => $subscriptionPackage->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays($request->trial_days),
            'starts_at' => now(),
            'expires_at' => now()->addDays($request->trial_days),
            'amount_paid' => 0,
            'auto_renew' => true,
            'module_access' => $subscriptionPackage->enabled_modules ?? [],
        ]);

        Log::info('Business added to subscription package', [
            'business_id' => $business->id,
            'package_id' => $subscriptionPackage->id,
            'subscription_id' => $subscription->id,
            'trial_days' => $request->trial_days
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Business added to package successfully!',
            'subscription' => $subscription
        ]);
    }

    /**
     * Extend trial period for a business subscription
     */
    public function extendTrial(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|exists:business_subscriptions,id',
                'extension_days' => 'required|numeric|min:1|max:365',
                'reason' => 'nullable|string|max:500'
            ]);

            $subscription = \App\Models\BusinessSubscription::findOrFail($request->subscription_id);

            if ($subscription->status !== 'trial') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only extend trial subscriptions'
                ], 400);
            }

            if (!$subscription->trial_ends_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trial end date is not set for this subscription'
                ], 400);
            }

            // Extend trial period
            $newTrialEndsAt = $subscription->trial_ends_at->copy()->addDays((int)$request->extension_days);
            $subscription->update([
                'trial_ends_at' => $newTrialEndsAt,
                'expires_at' => $newTrialEndsAt,
            ]);

            Log::info('Trial extended for business subscription', [
                'subscription_id' => $subscription->id,
                'business_id' => $subscription->business_id,
                'extension_days' => $request->extension_days,
                'old_trial_ends_at' => $subscription->trial_ends_at->format('Y-m-d H:i:s'),
                'new_trial_ends_at' => $newTrialEndsAt->format('Y-m-d H:i:s'),
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Trial extended successfully!',
                'new_trial_ends_at' => $newTrialEndsAt->format('M d, Y')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Trial extension validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', collect($e->errors())->flatten()->toArray())
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Trial extension failed', [
                'subscription_id' => $request->subscription_id,
                'extension_days' => $request->extension_days,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while extending the trial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a pending subscription
     */
    public function approveSubscription(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|exists:business_subscriptions,id'
            ]);

            $subscription = BusinessSubscription::findOrFail($request->subscription_id);

            if ($subscription->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending subscriptions can be approved'
                ], 400);
            }

            // Update subscription status to active
            $subscription->update([
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
            ]);

            Log::info('Subscription approved by super admin', [
                'subscription_id' => $subscription->id,
                'business_id' => $subscription->business_id,
                'package_id' => $subscription->subscription_package_id,
                'approved_by' => auth('super_admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription approved successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription approval failed', [
                'subscription_id' => $request->subscription_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while approving the subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a pending subscription
     */
    public function rejectSubscription(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|exists:business_subscriptions,id',
                'reason' => 'required|string|max:500'
            ]);

            $subscription = BusinessSubscription::findOrFail($request->subscription_id);

            if ($subscription->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending subscriptions can be rejected'
                ], 400);
            }

            // Update subscription status to cancelled (rejected)
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason
            ]);

            Log::info('Subscription rejected by super admin', [
                'subscription_id' => $subscription->id,
                'business_id' => $subscription->business_id,
                'package_id' => $subscription->subscription_package_id,
                'reason' => $request->reason,
                'rejected_by' => auth('super_admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription rejected successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription rejection failed', [
                'subscription_id' => $request->subscription_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while rejecting the subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove business from subscription package
     */
    public function removeBusiness(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:business_subscriptions,id',
            'reason' => 'required|string|max:500'
        ]);

        $subscription = \App\Models\BusinessSubscription::findOrFail($request->subscription_id);

        // Cancel the subscription
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason
        ]);

        Log::info('Business removed from subscription package', [
            'subscription_id' => $subscription->id,
            'business_id' => $subscription->business_id,
            'package_id' => $subscription->subscription_package_id,
            'reason' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Business removed from package successfully!'
        ]);
    }

    /**
     * Search businesses for adding to packages
     */
    public function searchBusinesses(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'businesses' => []
            ]);
        }

        $businesses = \App\Models\Business::where('business_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->where('status', 'active')
            ->select('id', 'business_name', 'email')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'businesses' => $businesses
        ]);
    }
}
