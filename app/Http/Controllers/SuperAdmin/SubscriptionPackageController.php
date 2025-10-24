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
}
