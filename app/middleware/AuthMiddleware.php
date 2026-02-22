<?php
/**
 * Sanskar AI - Auth Middleware
 * =============================
 * Ensures user is authenticated
 */

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Router;

class AuthMiddleware
{
    /**
     * Handle the middleware check
     */
    public function handle(): bool
    {
        if (!Auth::check()) {
            // Store intended URL
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            
            // Redirect to login
            Router::redirect('/login');
            return false;
        }
        
        return true;
    }
}
