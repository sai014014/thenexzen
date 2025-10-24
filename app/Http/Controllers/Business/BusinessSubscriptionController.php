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

        // Get available packages for upgrade
        $availablePackages = SubscriptionPackage::where('status', 'active')
            ->where('id', '!=', $currentSubscription?->subscription_package_id)
            ->get();

        // Get subscription history
        $subscriptionHistory = BusinessSubscription::where('business_id', $business->id)
            ->with('subscriptionPackage')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('business.subscription.index', compact(
            'currentSubscription',
            'availablePackages',
            'subscriptionHistory'
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
            'trial_ends_at' => now()->addDays($package->trial_period_days),
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
}
