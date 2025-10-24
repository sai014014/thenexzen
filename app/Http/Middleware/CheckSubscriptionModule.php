<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessSubscription;

class CheckSubscriptionModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $module
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $module)
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
            return $this->handleNoSubscription($request, $module);
        }

        // Check if module is accessible
        if (!$subscription->canAccessModule($module)) {
            return $this->handleModuleNotAccessible($request, $module, $subscription);
        }

        return $next($request);
    }

    private function handleNoSubscription(Request $request, string $module)
    {
        $moduleName = ucwords(str_replace('_', ' ', $module));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "No active subscription found. Please subscribe to access the {$moduleName} module.",
                'redirect' => route('business.subscription.index')
            ], 403);
        }

        return redirect()->route('business.subscription.index')
            ->with('error', "No active subscription found. Please subscribe to access the {$moduleName} module.");
    }

    private function handleModuleNotAccessible(Request $request, string $module, BusinessSubscription $subscription)
    {
        $package = $subscription->subscriptionPackage;
        $moduleName = $package->getModuleDisplayName($module);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "The {$moduleName} module is not available in your current plan ({$package->package_name}). Please upgrade to access this module.",
                'redirect' => route('business.subscription.index')
            ], 403);
        }

        return redirect()->route('business.subscription.index')
            ->with('error', "The {$moduleName} module is not available in your current plan ({$package->package_name}). Please upgrade to access this module.");
    }
}
