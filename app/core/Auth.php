<?php
/**
 * Sanskar AI - Authentication Helper
 * ====================================
 * Session management, role checking, and security utilities
 */

namespace App\Core;

use App\Config\App;
use App\Config\Database;

class Auth
{
    private const SESSION_KEY = 'sai_user';
    private const CSRF_KEY = 'sai_csrf_token';
    
    /**
     * Initialize authentication
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Attempt login with credentials
     */
    public static function attempt(string $email, string $password): bool
    {
        self::init();
        
        $sql = "SELECT * FROM SAI_users WHERE email = :email LIMIT 1";
        $user = Database::queryOne($sql, ['email' => $email]);
        
        if (!$user) {
            return false;
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }
        
        // Check if user is blocked
        if ($user['status'] === App::STATUS_BLOCKED) {
            return false;
        }
        
        // Store user in session
        $_SESSION[self::SESSION_KEY] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['status'],
        ];
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        return true;
    }
    
    /**
     * Attempt login with mobile
     */
    public static function attemptWithMobile(string $mobile, string $password): bool
    {
        self::init();
        
        $sql = "SELECT * FROM SAI_users WHERE mobile = :mobile LIMIT 1";
        $user = Database::queryOne($sql, ['mobile' => $mobile]);
        
        if (!$user) {
            return false;
        }
        
        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }
        
        // Check if user is blocked
        if ($user['status'] === App::STATUS_BLOCKED) {
            return false;
        }
        
        // Store user in session
        $_SESSION[self::SESSION_KEY] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['status'],
        ];
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        return true;
    }
    
    /**
     * Logout current user
     */
    public static function logout(): void
    {
        self::init();
        
        unset($_SESSION[self::SESSION_KEY]);
        unset($_SESSION[self::CSRF_KEY]);
        
        session_regenerate_id(true);
    }
    
    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        self::init();
        
        return isset($_SESSION[self::SESSION_KEY]) && !empty($_SESSION[self::SESSION_KEY]['id']);
    }
    
    /**
     * Check if user is guest
     */
    public static function guest(): bool
    {
        return !self::check();
    }
    
    /**
     * Get authenticated user
     */
    public static function user(): ?array
    {
        self::init();
        
        if (!self::check()) {
            return null;
        }
        
        return $_SESSION[self::SESSION_KEY];
    }
    
    /**
     * Get authenticated user ID
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int) $user['id'] : null;
    }
    
    /**
     * Get user role
     */
    public static function role(): ?string
    {
        $user = self::user();
        return $user ? $user['role'] : null;
    }
    
    /**
     * Check if user has specific role
     */
    public static function hasRole(string $role): bool
    {
        return self::role() === $role;
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin(): bool
    {
        return self::hasRole(App::ROLE_ADMIN);
    }
    
    /**
     * Check if user is pandit
     */
    public static function isPandit(): bool
    {
        return self::hasRole(App::ROLE_PANDIT);
    }
    
    /**
     * Check if user is regular user
     */
    public static function isUser(): bool
    {
        return self::hasRole(App::ROLE_USER);
    }
    
    /**
     * Check if pandit is approved
     */
    public static function isPanditApproved(): bool
    {
        if (!self::isPandit()) {
            return false;
        }
        
        $userId = self::id();
        $sql = "SELECT approval_status FROM SAI_pandit_profiles WHERE user_id = :user_id LIMIT 1";
        $profile = Database::queryOne($sql, ['user_id' => $userId]);
        
        return $profile && $profile['approval_status'] === App::APPROVAL_APPROVED;
    }
    
    /**
     * Generate CSRF token
     */
    public static function csrfToken(): string
    {
        self::init();
        
        if (!isset($_SESSION[self::CSRF_KEY])) {
            $_SESSION[self::CSRF_KEY] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION[self::CSRF_KEY];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken(string $token): bool
    {
        self::init();
        
        if (!isset($_SESSION[self::CSRF_KEY])) {
            return false;
        }
        
        return hash_equals($_SESSION[self::CSRF_KEY], $token);
    }
    
    /**
     * Regenerate CSRF token
     */
    public static function regenerateCsrfToken(): string
    {
        self::init();
        
        $_SESSION[self::CSRF_KEY] = bin2hex(random_bytes(32));
        return $_SESSION[self::CSRF_KEY];
    }
    
    /**
     * Hash password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Get full user data from database
     */
    public static function fullUser(): ?array
    {
        if (!self::check()) {
            return null;
        }
        
        $sql = "SELECT * FROM SAI_users WHERE id = :id LIMIT 1";
        return Database::queryOne($sql, ['id' => self::id()]);
    }
    
    /**
     * Refresh session user data
     */
    public static function refresh(): void
    {
        if (!self::check()) {
            return;
        }
        
        $user = self::fullUser();
        
        if ($user) {
            $_SESSION[self::SESSION_KEY] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'status' => $user['status'],
            ];
        }
    }
    
    /**
     * Get dashboard URL based on role
     */
    public static function dashboardUrl(): string
    {
        switch (self::role()) {
            case App::ROLE_ADMIN:
                return '/admin/dashboard';
            case App::ROLE_PANDIT:
                return '/pandit/dashboard';
            case App::ROLE_USER:
                return '/user/dashboard';
            default:
                return '/';
        }
    }
    
    /**
     * Generate CSRF hidden input
     */
    public static function csrfField(): string
    {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars(self::csrfToken()) . '">';
    }
}
