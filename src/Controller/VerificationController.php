<?php

namespace PocketPHP\Controller;

use PocketPHP\Model\User;

class VerificationController {
    
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new User();
    }

    // Send verification email
    public function sendVerificationEmail($email) {
        // Generate verification token
        $token = bin2hex(random_bytes(32));
        
        // Save token to session (for verification)
        $_SESSION['verification_token'] = $token;
        $_SESSION['verification_email'] = $email;
        
        // Create verification URL
        $verificationUrl = "http://" . $_SERVER['HTTP_HOST'] . "/verify-email?token=" . $token . "&email=" . urlencode($email);
        
        try {
            // Get user details
            $userModel = new \PocketPHP\Model\User();
            $user = $userModel->findByEmail($email);
            
            if (!$user) {
                error_log("User not found: " . $email);
                return false;
            }
            
            // Send verification email
            $emailService = new \PocketPHP\Services\EmailService();
            
            $result = $emailService->sendVerificationEmail(
                $email,
                $user->name,
                $verificationUrl
            );
            
            // Store URL in session (for testing)
            $_SESSION['verification_url'] = $verificationUrl;
            
            return $result;
            
        } catch (\Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            // For development, still store the URL in session
            $_SESSION['verification_url'] = $verificationUrl;
            return false;
        }
    }
    
    // Show verification page
    public function showVerificationNotice() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        $email = $_SESSION['user_email'] ?? '';
        $verified = $this->userModel->isEmailVerified($email);
        
        if ($verified) {
            header('Location: /');
            exit;
        }
        
        // Resend verification email if not already sent
        if (empty($_SESSION['verification_sent'])) {
            $this->sendVerificationEmail($email);
            $_SESSION['verification_sent'] = true;
            $_SESSION['message'] = 'A verification link has been sent to your email address.';
        }
        
        require __DIR__.'/../../templates/auth/verify-email.php';
    }
    
    // Verify email
    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';
        
        // In a real app, you would verify the token from the database
        // For now, we'll just check the session
        if ($token === ($_SESSION['verification_token'] ?? '') && 
            $email === ($_SESSION['verification_email'] ?? '')) {
            
            // Mark email as verified
            $this->userModel->markEmailAsVerified($email);
            
            // Clear the token
            unset($_SESSION['verification_token']);
            unset($_SESSION['verification_email']);
            
            $_SESSION['success'] = 'Your email has been verified successfully!';
            header('Location: /');
            exit;
        }
        
        $_SESSION['error'] = 'Invalid or expired verification link.';
        header('Location: /');
        exit;
    }
    
    // Resend verification email
    public function resendVerification() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        $email = $_SESSION['user_email'] ?? '';
        
        if ($this->userModel->isEmailVerified($email)) {
            header('Location: /');
            exit;
        }
        
        $this->sendVerificationEmail($email);
        $_SESSION['message'] = 'A new verification link has been sent to your email address.';
        header('Location: /email/verify');
        exit;
    }
}
