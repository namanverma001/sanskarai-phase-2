<?php
/**
 * Sanskar AI - Pandit Middleware
 * ================================
 * Protects pandit-specific routes with approval check
 */

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Router;
use App\Config\App;

class PanditMiddleware
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
        
        // Check if user is pandit
        if (!Auth::isPandit()) {
            $_SESSION['flash']['error'] = 'Access denied. Pandit account required.';
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
        
        // Check if pandit is approved
        if (!Auth::isPanditApproved()) {
            $_SESSION['flash']['warning'] = 'Your pandit profile is pending approval. Some features are restricted.';
            // Allow access but with limited functionality
            // The controller should check approval status for specific features
        }
        
        return true;
    }
}
