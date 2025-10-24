<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessSubscription;
use App\Models\SubscriptionPackage;

class CheckSubscriptionChanges
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return $next($request);
        }

        $business = $businessAdmin->business;
        
        // Get current subscription
        $subscription = BusinessSubscription::where('business_id', $business->id)
            ->whereIn('status', ['active', 'trial'])
            ->with('subscriptionPackage')
            ->first();

        if ($subscription && $subscription->subscriptionPackage) {
            $package = $subscription->subscriptionPackage;
            
            // Check if package modules have changed
            $currentModules = $subscription->module_access ?? [];
            $packageModules = $package->enabled_modules ?? [];
            
            // If modules don't match, update the subscription
            if ($currentModules !== $packageModules) {
                $subscription->update([
                    'module_access' => $packageModules
                ]);
                
                // Log the sync
                \Log::info('Subscription synced with package changes', [
                    'business_id' => $business->id,
                    'business_name' => $business->business_name,
                    'subscription_id' => $subscription->id,
                    'package_id' => $package->id,
                    'package_name' => $package->package_name,
                    'old_modules' => $currentModules,
                    'new_modules' => $packageModules
                ]);
            }
        }

        return $next($request);
    }
}
