<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessSubscription;

class CheckBusinessSubscription
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
            return redirect()->route('business.login');
        }

        $business = $businessAdmin->business;

        if (!$business) {
            return redirect()->route('business.login');
        }

        // Check if business has an active subscription
        $subscription = $business->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->first();

        // If no active subscription, redirect to subscription page
        if (!$subscription) {
            return redirect()->route('business.subscription.index');
        }

        // If trial has ended, redirect to subscription page
        if ($subscription->status === 'trial' && $subscription->is_expired) {
            return redirect()->route('business.subscription.index')
                ->with('warning', 'Your trial period has ended. Please choose a subscription plan to continue.');
        }

        return $next($request);
    }
}
