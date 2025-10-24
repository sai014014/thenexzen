<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessSubscription;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessSubscriptionController extends Controller
{
    public function index()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Get current subscription
        $currentSubscription = BusinessSubscription::where('business_id', $business->id)
            ->whereIn('status', ['active', 'trial'])
            ->with('subscriptionPackage')
            ->first();

        // Determine if this is a new user (no subscription history)
        $hasSubscriptionHistory = BusinessSubscription::where('business_id', $business->id)->exists();
        
        // Get available packages
        $availablePackages = SubscriptionPackage::where('status', 'active')->get();

        // Get subscription history
        $subscriptionHistory = BusinessSubscription::where('business_id', $business->id)
            ->with('subscriptionPackage')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('business.subscription.index', compact(
            'currentSubscription',
            'availablePackages',
            'subscriptionHistory',
            'hasSubscriptionHistory'
        ));
    }

    public function show(BusinessSubscription $subscription)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the subscription belongs to this business
        if ($subscription->business_id !== $business->id) {
            abort(403, 'Unauthorized access');
        }

        return view('business.subscription.show', compact('subscription'));
    }

    public function startTrial(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        $request->validate([
            'package_id' => 'required|exists:subscription_packages,id'
        ]);

        $package = SubscriptionPackage::findOrFail($request->package_id);

        // Check if package is available
        if ($package->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Selected package is not available'], 400);
        }

        // Check if business already has a subscription
        $existingSubscription = BusinessSubscription::where('business_id', $business->id)->exists();
        
        if ($existingSubscription) {
            return response()->json(['success' => false, 'message' => 'You have already used your trial period'], 400);
        }

        // Create trial subscription
        $trialEndsAt = now()->addDays($package->trial_period_days ?? 14);
        $trialSubscription = BusinessSubscription::create([
            'business_id' => $business->id,
            'subscription_package_id' => $package->id,
            'status' => 'trial',
            'trial_ends_at' => $trialEndsAt,
            'starts_at' => now(),
            'expires_at' => $trialEndsAt, // For trial subscriptions, expires_at should match trial_ends_at
            'amount_paid' => 0,
            'auto_renew' => false,
            'module_access' => $package->getEnabledModules(),
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Trial started successfully! You have ' . ($package->trial_period_days ?? 14) . ' days to explore our features.',
            'subscription' => $trialSubscription
        ]);
    }

    public function cancel(Request $request, BusinessSubscription $subscription)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the subscription belongs to this business
        if ($subscription->business_id !== $business->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $subscription->cancel($request->cancellation_reason);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully. It will remain active until ' . $subscription->expires_at->format('M d, Y') . '.'
        ]);
    }

    public function upgrade(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        $request->validate([
            'package_id' => 'required|exists:subscription_packages,id'
        ]);

        $package = SubscriptionPackage::findOrFail($request->package_id);

        // Check if package is available
        if ($package->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Selected package is not available'], 400);
        }

        // Get current subscription
        $currentSubscription = BusinessSubscription::where('business_id', $business->id)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if ($currentSubscription) {
            // Cancel current subscription
            $currentSubscription->cancel('Upgraded to ' . $package->package_name);
        }

        // Create new subscription
        $newSubscription = BusinessSubscription::create([
            'business_id' => $business->id,
            'subscription_package_id' => $package->id,
            'status' => 'pending',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'amount_paid' => 0,
            'auto_renew' => true,
            'module_access' => $package->getEnabledModules(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription upgrade initiated successfully!',
            'subscription' => $newSubscription
        ]);
    }

    public function renew(BusinessSubscription $subscription)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the subscription belongs to this business
        if ($subscription->business_id !== $business->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        if ($subscription->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Only active subscriptions can be renewed'], 400);
        }

        $subscription->renew();

        return response()->json([
            'success' => true,
            'message' => 'Subscription renewed successfully!',
            'new_expiry' => $subscription->expires_at->format('M d, Y')
        ]);
    }

    public function getAvailablePackages()
    {
        $packages = SubscriptionPackage::where('status', 'active')
            ->select('id', 'package_name', 'subscription_fee', 'currency', 'description', 'enabled_modules')
            ->get();

        return response()->json([
            'success' => true,
            'packages' => $packages
        ]);
    }

    /**
     * Pause the current subscription
     */
    public function pause(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        // Get current active subscription
        $subscription = BusinessSubscription::where('business_id', $business->id)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found'
            ], 400);
        }

        if ($subscription->is_paused) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription is already paused'
            ], 400);
        }

        if ($subscription->pause($request->reason)) {
            return response()->json([
                'success' => true,
                'message' => 'Subscription paused successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to pause subscription'
        ], 500);
    }

    /**
     * Resume the current subscription
     */
    public function resume()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Get current paused subscription
        $subscription = BusinessSubscription::where('business_id', $business->id)
            ->whereIn('status', ['active', 'trial'])
            ->where('is_paused', true)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No paused subscription found'
            ], 400);
        }

        if ($subscription->resume()) {
            return response()->json([
                'success' => true,
                'message' => 'Subscription resumed successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to resume subscription'
        ], 500);
    }
}
