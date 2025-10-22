<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Check if the request is for business admin routes
            if ($request->is('business/*')) {
                return route('business.login');
            }
            // Check if the request is for super admin routes
            if ($request->is('super-admin/*')) {
                return route('super-admin.login');
            }
            // Default to business login
            return route('business.login');
        }
    }
}
