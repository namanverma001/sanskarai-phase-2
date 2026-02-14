<?php
/**
 * Sanskar AI - Application Configuration
 * =======================================
 * Core application settings and constants
 */

namespace App\Config;

class App
{
    // Application info
    public const APP_NAME = 'Sanskar AI';
    public const APP_VERSION = '1.0.0';
    
    // User roles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PANDIT = 'pandit';
    public const ROLE_USER = 'user';
    
    // User statuses
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_PENDING = 'pending';
    
    // Pandit approval statuses
    public const APPROVAL_PENDING = 'pending';
    public const APPROVAL_APPROVED = 'approved';
    public const APPROVAL_REJECTED = 'rejected';
    
    // Paths
    private static ?string $basePath = null;
    
    /**
     * Get base application path
     */
    public static function basePath(string $path = ''): string
    {
        if (self::$basePath === null) {
            self::$basePath = dirname(__DIR__, 2);
        }
        
        return self::$basePath . ($path ? '/' . ltrim($path, '/') : '');
    }
    
    /**
     * Get app directory path
     */
    public static function appPath(string $path = ''): string
    {
        return self::basePath('app' . ($path ? '/' . ltrim($path, '/') : ''));
    }
    
    /**
     * Get public directory path
     */
    public static function publicPath(string $path = ''): string
    {
        return self::basePath('public' . ($path ? '/' . ltrim($path, '/') : ''));
    }
    
    /**
     * Get storage directory path
     */
    public static function storagePath(string $path = ''): string
    {
        return self::basePath('storage' . ($path ? '/' . ltrim($path, '/') : ''));
    }
    
    /**
     * Get views directory path
     */
    public static function viewsPath(string $path = ''): string
    {
        return self::appPath('views' . ($path ? '/' . ltrim($path, '/') : ''));
    }
    
    /**
     * Get environment variable with default
     */
    public static function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        // Convert string booleans
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
        }
        
        return $value;
    }
    
    /**
     * Check if app is in debug mode
     */
    public static function isDebug(): bool
    {
        return self::env('APP_DEBUG', false) === true;
    }
    
    /**
     * Get app URL
     */
    public static function url(string $path = ''): string
    {
        $baseUrl = self::env('APP_URL', 'http://localhost:8000');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
    
    /**
     * Get all available roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_PANDIT,
            self::ROLE_USER,
        ];
    }
    
    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_BLOCKED,
            self::STATUS_PENDING,
        ];
    }
    
    /**
     * Initialize application
     */
    public static function init(): void
    {
        // Load environment
        Database::loadEnv();
        
        // Set error reporting based on environment
        if (self::isDebug()) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }
        
        // Set timezone
        date_default_timezone_set('Asia/Kolkata');
        
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
