<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBusinessStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated as business admin
        if (Auth::guard('business_admin')->check()) {
            $businessAdmin = Auth::guard('business_admin')->user();
            $business = $businessAdmin->business;
            
            // Check if business exists and is active
            if (!$business || $business->status !== 'active') {
                Auth::guard('business_admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('business.login')->withErrors([
                    'email' => 'Your business account is currently inactive. Please contact support for assistance.',
                ]);
            }
            
            // Check if business admin is active
            if (!$businessAdmin->is_active) {
                Auth::guard('business_admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('business.login')->withErrors([
                    'email' => 'Your admin account is currently inactive. Please contact support for assistance.',
                ]);
            }
        }
        
        return $next($request);
    }
}
