<?php

namespace PocketPHP\Controller;

use PocketPHP\Model\User;
use PocketCookies\PocketCookies;
use PocketSecurity\Security;
use PocketErrorLog\ErrorLog;

class AuthController {
    
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new User();
        $this->handleRememberMe();
    }

    public function index() {
        require __DIR__.'/../../templates/auth/signup.php';
    }

    // Show login form
    public function loginForm() {
        require __DIR__.'/../../templates/auth/login.php';
    }

    // Handle remember me
    private function handleRememberMe() {
        if (empty($_SESSION['user_id']) && $cookie = PocketCookies::get('remember_me')) {
            // Format: "user_id|hashed_identifier"
            $parts = explode('|', $cookie);
                
            if (count($parts) === 2) {
                [$userId, $hashedIdentifier] = $parts;
                    
                $user = $this->userModel->findById($userId);
                if ($user) {
                    // Recreate what the hash should be
                    $expectedHash = $this->createRememberHash($user);
                        
                    // Compare hashes securely
                    if (hash_equals($expectedHash, $hashedIdentifier)) {
                        $this->setUserSession($user);
                    }
                }
            }
        }
    }

    // Create remember hash
    private function createRememberHash($user) {
        // Uses existing password hash as "secret" component
        $uniqueString = $user->id . $user->email . $user->password;
        return hash('sha256', $uniqueString);
    }
    
    // Set remember cookie
    private function setRememberCookie($user) {
        $hashedIdentifier = $this->createRememberHash($user);
        $value = "{$user->id}|{$hashedIdentifier}";
        
        PocketCookies::set('remember_me', $value, 30); // 30 days
    }
    
    private function setUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
    }


    
    // Register user
    public function store() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Debug: Log session and POST data
        ErrorLog::log('=== SIGNUP DEBUG ===');
        ErrorLog::log('Session ID: ' . session_id());
        ErrorLog::log('POST Data: ' . print_r($_POST, true));
        ErrorLog::log('Session Data: ' . print_r($_SESSION, true));
        
        // Verify CSRF token exists in both POST and SESSION
        if (!isset($_POST['csrf_token']) || empty($_POST['csrf_token'])) {
            error_log('CSRF Error: No token in POST data');
            $_SESSION['error'] = 'Security token is required.';
            header('Location: /auth/signup');
            exit;
        }
        
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            error_log('CSRF Error: No token in session');
            $_SESSION['error'] = 'Session expired. Please refresh the page and try again.';
            header('Location: /auth/signup');
            exit;
        }
        
        // Verify the tokens match
        $postToken = trim($_POST['csrf_token']);
        $sessionToken = trim($_SESSION['csrf_token']);
        
        ErrorLog::log('Comparing tokens:');
        ErrorLog::log('POST Token: [' . $postToken . ']');
        ErrorLog::log('Session Token: [' . $sessionToken . ']');
        ErrorLog::log('Token length - POST: ' . strlen($postToken) . ', SESSION: ' . strlen($sessionToken));
        
        if ($postToken !== $sessionToken) {
            ErrorLog::log('CSRF Token mismatch');
            $_SESSION['error'] = 'Invalid security token. Please refresh the page and try again.';
            header('Location: /auth/signup');
            exit;
        }
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Basic validation
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /auth/signup');
            exit;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address';
            header('Location: /auth/signup');
            exit;
        }
        
        // Check password length
        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters long';
            header('Location: /auth/signup');
            exit;
        }
        
        // Check password confirmation
        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: /auth/signup');
            exit;
        }
        
        // Check if email already exists
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser) {
            $_SESSION['error'] = 'Email already registered';
            header('Location: /auth/signup');
            exit;
        }
        
        // Create the user
        $user = $this->userModel->create($name, $email, $password);
        if ($user) {
            // Send verification email
            $verification = new \PocketPHP\Controller\VerificationController();
            $verification->sendVerificationEmail($email);
            
            // Set user session with sanitized data
            $user = $this->userModel->findByEmail($email);
            if ($user) {
                $_SESSION['user_id'] = (int)$user->id;
                $_SESSION['user_name'] = Security::sanitizeOutput($user->name);
                $_SESSION['user_email'] = Security::sanitizeOutput($user->email);
                
                // Set a success message
                $_SESSION['message'] = 'A verification link has been sent to your email address.';
            }
            header('Location: /email/verify');
            exit;
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            header('Location: /auth/signup');
            exit;
        }
    }

    // Login user
    public function login() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Debug: Log session and POST data
        error_log('=== CSRF DEBUG ===');
        error_log('Session ID: ' . session_id());
        error_log('POST Data: ' . print_r($_POST, true));
        error_log('Session Data: ' . print_r($_SESSION, true));
        
        // Verify CSRF token exists in both POST and SESSION
        if (!isset($_POST['csrf_token']) || empty($_POST['csrf_token'])) {
            error_log('CSRF Error: No token in POST data');
            $_SESSION['error'] = 'Security token is required.';
            header('Location: /auth/login');
            exit;
        }
        
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            error_log('CSRF Error: No token in session');
            $_SESSION['error'] = 'Session expired. Please refresh the page and try again.';
            header('Location: /auth/login');
            exit;
        }
        
        // Verify the tokens match
        $postToken = trim($_POST['csrf_token']);
        $sessionToken = trim($_SESSION['csrf_token']);
        
        error_log('Comparing tokens:');
        error_log('POST Token: [' . $postToken . ']');
        error_log('Session Token: [' . $sessionToken . ']');
        error_log('Token length - POST: ' . strlen($postToken) . ', SESSION: ' . strlen($sessionToken));
        
        if ($postToken !== $sessionToken) {
            error_log('CSRF Token mismatch');
            $_SESSION['error'] = 'Invalid security token. Please refresh the page and try again.';
            header('Location: /auth/login');
            exit;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user->password)) {
            // Set user session with sanitized data
            $_SESSION['user_id'] = (int)$user->id;
            $_SESSION['user_name'] = is_string($user->name) ? Security::sanitizeOutput($user->name) : '';
            $_SESSION['user_email'] = is_string($user->email) ? Security::sanitizeOutput($user->email) : '';
            
            // Check if email is verified
            if (empty($user->email_verified_at)) {
                // Redirect to verification notice
                header('Location: /email/verify');
                exit;
            }
            
            header('Location: /');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: /auth/login');
            exit;
        }
    }

    // Logout user
    public function logout() {
        // Clear remember me cookie on logout
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/');
            unset($_COOKIE['remember_me']);
        }
        
        // Destroy the session
        Security::destroySession();
        
        // Redirect to home page
        header('Location: /');
        exit;
    }



}
