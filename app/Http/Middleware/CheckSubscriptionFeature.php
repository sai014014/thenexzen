<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessSubscription;

class CheckSubscriptionFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $feature
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $feature)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login');
        }

        $business = $businessAdmin->business;
        
        // Get current active subscription
        $subscription = BusinessSubscription::where('business_id', $business->id)
            ->whereIn('status', ['active', 'trial'])
            ->with('subscriptionPackage')
            ->first();

        if (!$subscription) {
            return $this->handleNoSubscription($request, $feature);
        }

        // Check if feature is available
        if (!$subscription->canUseFeature($feature)) {
            return $this->handleFeatureNotAvailable($request, $feature, $subscription);
        }

        return $next($request);
    }

    private function handleNoSubscription(Request $request, string $feature)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found. Please subscribe to access this feature.',
                'redirect' => route('business.subscription.index')
            ], 403);
        }

        return redirect()->route('business.subscription.index')
            ->with('error', 'No active subscription found. Please subscribe to access this feature.');
    }

    private function handleFeatureNotAvailable(Request $request, string $feature, BusinessSubscription $subscription)
    {
        $package = $subscription->subscriptionPackage;
        $featureName = ucwords(str_replace('_', ' ', $feature));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "The {$featureName} feature is not available in your current plan ({$package->package_name}). Please upgrade to access this feature.",
                'redirect' => route('business.subscription.index')
            ], 403);
        }

        return redirect()->route('business.subscription.index')
            ->with('error', "The {$featureName} feature is not available in your current plan ({$package->package_name}). Please upgrade to access this feature.");
    }
}
