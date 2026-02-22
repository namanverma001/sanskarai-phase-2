<?php
/**
 * Sanskar AI - Admin Middleware
 * ===============================
 * Protects admin-only routes
 */

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Router;
use App\Config\App;

class AdminMiddleware
{
    /**
     * Handle the middleware check
     */
    public function handle(): bool
    {
        // First check if authenticated
        if (!Auth::check()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            Router::redirect('/login');
            return false;
        }
        
        // Check if user is admin
        if (!Auth::isAdmin()) {
            // Set error message
            $_SESSION['flash']['error'] = 'Access denied. Admin privileges required.';
            
            // Redirect to appropriate dashboard based on role
            Router::redirect(Auth::dashboardUrl());
            return false;
        }
        
        // Check if user is blocked
        $user = Auth::user();
        if ($user['status'] === App::STATUS_BLOCKED) {
            Auth::logout();
            $_SESSION['flash']['error'] = 'Your account has been blocked.';
            Router::redirect('/login');
            return false;
        }
        
        return true;
    }
}
