<?php
/**
 * Sanskar AI - Auth Controller
 * ==============================
 * Handles login, signup, and logout
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Router;
use App\Models\User;
use App\Models\PanditProfile;
use App\Config\App;

class AuthController extends Controller
{
    private User $userModel;
    private PanditProfile $panditProfileModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->panditProfileModel = new PanditProfile();
    }
    
    /**
     * Show login form
     */
    public function showLogin(): void
    {
        $this->viewWithLayout('auth/login', 'layouts/auth', [
            'title' => 'Login - Sanskar AI',
        ]);
    }
    
    /**
     * Handle login
     */
    public function login(): void
    {
        // Verify CSRF
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token. Please try again.']);
            return;
        }
        
        $identifier = $this->input('identifier'); // Email or mobile
        $password = $this->input('password');
        
        // Validate inputs
        $errors = $this->validate([
            'identifier' => $identifier,
            'password' => $password,
        ], [
            'identifier' => 'required',
            'password' => 'required|min:6',
        ]);
        
        if (!empty($errors)) {
            $this->back(['error' => 'Please fill in all required fields correctly.', 'errors' => $errors]);
            return;
        }
        
        // Try to login with email first
        $success = Auth::attempt($identifier, $password);
        
        // If failed, try with mobile
        if (!$success && preg_match('/^[0-9]{10}$/', $identifier)) {
            $success = Auth::attemptWithMobile($identifier, $password);
        }
        
        if ($success) {
            // Update last login
            $this->userModel->updateLastLogin(Auth::id());
            
            // Redirect to intended URL or landing page (where navbar shows user dropdown)
            $intendedUrl = $_SESSION['intended_url'] ?? '/';
            unset($_SESSION['intended_url']);
            
            Router::redirect($intendedUrl);
        } else {
            $this->back(['error' => 'Invalid credentials or account is blocked.']);
        }
    }
    
    /**
     * Show signup form
     */
    public function showSignup(): void
    {
        $this->viewWithLayout('auth/signup', 'layouts/auth', [
            'title' => 'Sign Up - Sanskar AI',
        ]);
    }
    
    /**
     * Handle user signup
     */
    public function signup(): void
    {
        // Verify CSRF
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token. Please try again.']);
            return;
        }
        
        $data = $this->only(['name', 'email', 'mobile', 'password', 'password_confirmation', 'role']);
        
        // Validate inputs
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'mobile' => 'required|min:10|max:15',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:user,pandit',
        ]);
        
        // Check if email already exists
        if ($this->userModel->findByEmail($data['email'])) {
            $errors['email'][] = 'This email is already registered.';
        }
        
        // Check if mobile already exists
        if ($this->userModel->findByMobile($data['mobile'])) {
            $errors['mobile'][] = 'This mobile number is already registered.';
        }
        
        if (!empty($errors)) {
            $this->back([
                'error' => 'Please correct the errors below.',
                'errors' => $errors,
                'old' => $data,
            ]);
            return;
        }
        
        try {
            // Create user
            $userId = $this->userModel->createUser([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'password' => $data['password'],
                'role' => $data['role'],
                'status' => App::STATUS_ACTIVE,
            ]);
            
            // If pandit, create profile
            if ($data['role'] === App::ROLE_PANDIT) {
                $specialization = $this->input('specialization', 'General Puja');
                $experience = (int) $this->input('experience_years', 0);
                $bio = $this->input('bio', '');
                
                $this->panditProfileModel->createProfile($userId, [
                    'specialization' => $specialization,
                    'experience_years' => $experience,
                    'bio' => $bio,
                ]);
                
                // Login and redirect to landing page with message
                Auth::attempt($data['email'], $data['password']);
                $this->redirect('/', [
                    'success' => 'Account created successfully! Your profile is pending admin approval.',
                ]);
            } else {
                // Login and redirect to landing page
                Auth::attempt($data['email'], $data['password']);
                $this->redirect('/', [
                    'success' => 'Account created successfully! Welcome to Sanskar AI.',
                ]);
            }
            
        } catch (\Exception $e) {
            $this->back([
                'error' => 'Failed to create account. Please try again.',
                'old' => $data,
            ]);
        }
    }
    
    /**
     * Handle logout
     */
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login', ['success' => 'You have been logged out successfully.']);
    }
    
    /**
     * Show forgot password form
     */
    public function showForgotPassword(): void
    {
        $this->viewWithLayout('auth/forgot-password', 'layouts/auth', [
            'title' => 'Forgot Password - Sanskar AI',
        ]);
    }
    
    /**
     * Handle forgot password
     */
    public function forgotPassword(): void
    {
        // Verify CSRF
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }
        
        $email = $this->input('email');
        
        $user = $this->userModel->findByEmail($email);
        
        // Always show success message for security
        $this->back([
            'success' => 'If an account exists with this email, you will receive password reset instructions.',
        ]);
        
        // TODO: Implement email sending for password reset
    }
}
