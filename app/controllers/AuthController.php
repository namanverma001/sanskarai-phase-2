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
use App\Services\MailService;


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
        
        $data = $this->only(['name', 'email', 'mobile', 'community_name', 'kul_devi_devta', 'password', 'password_confirmation', 'role']);
        
        // Validate inputs
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'mobile' => 'required|min:10|max:15',
            'password' => 'required|min:8|confirmed',
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
                'community_name' => $data['community_name'] ?? null,
                'kul_devi_devta' => $data['kul_devi_devta'] ?? null,
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
                // Login and redirect to create family page for new users
                Auth::attempt($data['email'], $data['password']);
                $this->redirect('/user/families/create', [
                    'success' => 'Account created successfully! Please create your family profile to get started.',
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

        $email = trim($this->input('email'));

        if (!$email) {
            $this->back(['error' => 'Please enter your email address.']);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && $user['status'] === App::STATUS_ACTIVE) {
            // 1. Generate a cryptographically secure token
            $rawToken  = bin2hex(random_bytes(32)); // 64 hex chars
            $tokenHash = password_hash($rawToken, PASSWORD_DEFAULT);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            // 2. Store the hashed token in the DB
            $this->userModel->storeResetToken($user['id'], $tokenHash, $expiresAt);

            // 3. Build reset link and send email
            $appUrl    = rtrim($_ENV['APP_URL'] ?? 'http://sanskarai.com', '/');
            $resetLink = $appUrl . '/reset-password?token=' . urlencode($rawToken);

            // ✅ TESTING ONLY - logs the link so you can test without real email
            // ❌ REMOVE THIS LINE BEFORE GOING TO PRODUCTION
            file_put_contents(BASE_PATH . '/storage/logs/reset_debug.log', date('Y-m-d H:i:s') . " | $email | $resetLink\n", FILE_APPEND);

            $mailer = new MailService();
            $mailer->sendPasswordReset($email, $resetLink);
        }

        // Always show the same success message (prevents email enumeration)
        $this->back([
            'success' => 'If an account exists with this email, you will receive password reset instructions shortly.',
        ]);
    }

    /**
     * Show reset password form (validates token from URL)
     */
    public function showResetPassword(): void
    {
        $rawToken = $this->input('token'); // reads from $_GET (merged with $_POST by base controller)

        if (!$rawToken) {
            Router::redirect('/forgot-password');
            return;
        }

        // Validate the token
        $resetRow = $this->userModel->findValidResetToken($rawToken);

        if (!$resetRow) {
            $this->viewWithLayout('auth/reset-password', 'layouts/auth', [
                'title'   => 'Reset Password - Sanskar AI',
                'invalid' => true,
                'token'   => '',
            ]);
            return;
        }

        $this->viewWithLayout('auth/reset-password', 'layouts/auth', [
            'title' => 'Reset Password - Sanskar AI',
            'token' => $rawToken,
            'email' => $resetRow['email'],
        ]);
    }

    /**
     * Handle reset password form submission
     */
    public function resetPassword(): void
    {
        // Verify CSRF
        if (!$this->verifyCsrf()) {
            $this->back(['error' => 'Invalid security token.']);
            return;
        }

        $rawToken = $this->input('token');
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirmation');

        // Validate
        if (!$rawToken) {
            $this->redirect('/forgot-password', ['error' => 'Invalid or missing token.']);
            return;
        }

        if (!$password || strlen($password) < 8) {
            $this->back(['error' => 'Password must be at least 8 characters.', 'token' => $rawToken]);
            return;
        }

        if ($password !== $passwordConfirm) {
            $this->back(['error' => 'Passwords do not match.', 'token' => $rawToken]);
            return;
        }

        // Verify token again (race condition / expiry safety)
        $resetRow = $this->userModel->findValidResetToken($rawToken);

        if (!$resetRow) {
            $this->back(['error' => 'This reset link has expired or already been used. Please request a new one.', 'token' => '']);
            return;
        }

        // Update password and remove token
        $ok = $this->userModel->consumeResetToken(
            (int) $resetRow['user_id'],
            $password,
            (int) $resetRow['id']
        );

        if ($ok) {
            $this->redirect('/login', ['success' => 'Your password has been reset successfully. Please sign in with your new password.']);
        } else {
            $this->back(['error' => 'Something went wrong. Please try again.', 'token' => $rawToken]);
        }
    }
}
