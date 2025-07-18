<?php

namespace PocketPHP\Controller;

use PocketPHP\Model\User;

class AuthController {
    
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new User();
    }

    public function index() {
        require __DIR__.'/../../templates/auth/signup.php';
    }

    // Show login form
    public function loginForm() {
        require __DIR__.'/../../templates/auth/login.php';
    }

    // Register user
    public function store() {
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
            
            // Set user session
            $user = $this->userModel->findByEmail($email);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;
            
            // Redirect to verification notice
            $_SESSION['message'] = 'A verification link has been sent to your email address.';
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
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;
            
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
        session_destroy();
        header('Location: /');
        exit;
    }



}
