<?php
/**
 * Sanskar AI - Guest Middleware
 * ===============================
 * Ensures user is NOT authenticated (for login/signup pages)
 */

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Router;

class GuestMiddleware
{
    /**
     * Handle the middleware check
     */
    public function handle(): bool
    {
        if (Auth::check()) {
            // Redirect to appropriate dashboard
            Router::redirect(Auth::dashboardUrl());
            return false;
        }
        
        return true;
    }
}
