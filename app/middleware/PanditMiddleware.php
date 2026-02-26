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

        // Check if pandit is approved - only allow dashboard and profile for unapproved
        if (!Auth::isPanditApproved()) {
            $currentUri = strtok($_SERVER['REQUEST_URI'], '?');
            $currentUri = rtrim($currentUri, '/');

            $allowedRoutes = ['/pandit/dashboard', '/pandit/profile'];

            if (!in_array($currentUri, $allowedRoutes)) {
                $_SESSION['flash']['warning'] = 'Your profile is under review. Please wait for admin approval.';
                Router::redirect('/pandit/dashboard');
                return false;
            }
        }

        return true;
    }
}
