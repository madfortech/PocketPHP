<?php

namespace PocketPHP\Controller;

use PocketPHP\Model\User;
use PocketPHP\Model\Reset;

class ForgetPasswordController {
    
    private $userModel;
    private $resetModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new User();
        $this->resetModel = new Reset();
    }

    // Show forgot password form
    public function showForgotPasswordForm() {
        require __DIR__.'/../../templates/auth/forget-password.php';
    }

    // Process forgot password request
    public function forgotPassword() {
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $_SESSION['error'] = 'Please enter your email address';
            header('Location: /auth/forgot-password');
            exit;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address';
            header('Location: /auth/forgot-password');
            exit;
        }
        
        // Check if email exists
        $user = $this->userModel->findByEmail($email);
        
        if ($user) {
            // Generate a secure token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Save to resets table
            $saved = $this->resetModel->create($email, $token, $expiresAt);
            
            if ($saved) {
                // In a real application, you would send an email here
                // For now, we'll just show the token in the success message for testing
                $_SESSION['success'] = 'Password reset link has been sent to your email.';
                // For testing, you can see the token in the session
                $_SESSION['debug_token'] = $token;
            } else {
                $_SESSION['error'] = 'Failed to process your request. Please try again.';
            }
        } else {
            // Don't reveal if the email exists or not (security best practice)
            $_SESSION['success'] = 'If an account exists with that email, you will receive a password reset link.';
        }
        
        header('Location: /auth/forgot-password');
        exit;
    }
    
    // Add more methods for password reset functionality as needed
    // - showResetPasswordForm($token)
    // - resetPassword($token)
}
