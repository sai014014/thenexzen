<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckVehicleCapacity
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
        if (Auth::guard('business_admin')->check()) {
            $business = Auth::guard('business_admin')->user()->business;

            if ($business) {
                $subscription = $business->subscriptions()->whereIn('status', ['active', 'trial'])->first();

                if ($subscription && !$subscription->canAddVehicle()) {
                    $capacityStatus = $subscription->getVehicleCapacityStatus();
                    
                    Log::info('Vehicle capacity limit reached for business.', [
                        'business_id' => $business->id,
                        'current_count' => $capacityStatus['current'],
                        'capacity' => $capacityStatus['capacity'],
                        'route' => $request->route()->getName(),
                    ]);

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $capacityStatus['message'],
                            'capacity_status' => $capacityStatus
                        ], 403);
                    }

                    return redirect()->route('business.vehicles.index')
                        ->with('error', $capacityStatus['message']);
                }
            }
        }

        return $next($request);
    }
}
