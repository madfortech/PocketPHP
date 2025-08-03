<?php

namespace PocketPHP\Controller;

use PocketPHP\Model\User;
use PocketPHP\Model\Reset;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PocketErrorLog\ErrorLog;
use PocketSecurity\Security;

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
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verify CSRF token exists in both POST and SESSION
        if (!isset($_POST['csrf_token']) || empty($_POST['csrf_token'])) {
            ErrorLog::log('CSRF Error: No token in POST data');
            $_SESSION['error'] = 'Security token is required.';
            header('Location: /auth/forgot-password');
            exit;
        }
        
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            ErrorLog::log('CSRF Error: No token in session');
            $_SESSION['error'] = 'Session expired. Please refresh the page and try again.';
            header('Location: /auth/forgot-password');
            exit;
        }
        
        // Verify the tokens match
        $postToken = trim($_POST['csrf_token']);
        $sessionToken = trim($_SESSION['csrf_token']);
        
        if ($postToken !== $sessionToken) {
            ErrorLog::log('CSRF Token mismatch in forgot password');
            $_SESSION['error'] = 'Invalid security token. Please refresh the page and try again.';
            header('Location: /auth/forgot-password');
            exit;
        }
        
        $email = Security::sanitizeOutput(trim($_POST['email'] ?? ''));
        
        if (empty($email)) {
            ErrorLog::log('Please enter your email address');
            $_SESSION['error'] = 'Please enter your email address';
            header('Location: /auth/forgot-password');
            exit;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ErrorLog::log('Please enter a valid email address');
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
                // Send password reset email
                $emailSent = $this->sendPasswordResetEmail($email, $token);
                
                if ($emailSent) {
                    $_SESSION['success'] = 'Password reset link has been sent to your email. Please check your inbox.';
                    // For testing, you can see the token in the session (sanitized)
                    $_SESSION['debug_token'] = Security::sanitizeOutput($token);
                } else {
                    ErrorLog::log('Failed to send password reset email. Please try again.');
                    $_SESSION['error'] = 'Failed to send password reset email. Please try again.';
                }
            } else {
                ErrorLog::log('Failed to process your request. Please try again.');
                $_SESSION['error'] = 'Failed to process your request. Please try again.';
            }
        } else {
            ErrorLog::log('If an account exists with that email, you will receive a password reset link.');
            // Don't reveal if the email exists or not (security best practice)
            $_SESSION['success'] = 'If an account exists with that email, you will receive a password reset link.';
        }
        
        header('Location: /auth/forgot-password');
        exit;
    }
    
    // Show reset password form
    public function showResetPasswordForm($token = null) {
        // Get token from route parameter or query string and sanitize it
        $token = Security::sanitizeOutput($token ?? ($_GET['token'] ?? null));
        
        if (!$token) {
            ErrorLog::log('No reset token provided');
            $_SESSION['error'] = 'Invalid or missing reset token';
            header('Location: /auth/forgot-password');
            exit;
        }

        // Validate token
        $reset = $this->resetModel->findByToken($token);
        
        if (!$reset || strtotime($reset['expires_at']) < time()) {
            ErrorLog::log('Invalid or expired reset token');
            $_SESSION['error'] = 'Invalid or expired reset token';
            header('Location: /auth/forgot-password');
            exit;
        }
        
        // Store token in session for validation (sanitized)
        $_SESSION['reset_token'] = Security::sanitizeOutput($token);
        $_SESSION['reset_email'] = Security::sanitizeOutput($reset['email']);
        
        require __DIR__.'/../../templates/auth/reset-password.php';
    }
    
    // Process password reset
    public function resetPassword() {
        $token = Security::sanitizeOutput(trim($_POST['token'] ?? ''));
        $password = trim($_POST['password'] ?? ''); // Don't sanitize password
        $confirmPassword = trim($_POST['confirm_password'] ?? ''); // Don't sanitize password
        
        // Validate token
        $reset = $this->resetModel->findByToken($token);
        if (!$reset || strtotime($reset['expires_at']) < time()) {
            ErrorLog::log('Invalid or expired reset token');
            $_SESSION['error'] = 'Invalid or expired reset token';
            header('Location: /auth/forgot-password');
            exit;
        }
        
        // Validate passwords
        if (empty($password) || empty($confirmPassword)) {
            ErrorLog::log('Please fill in all fields');
            $_SESSION['error'] = 'Please fill in all fields';
            header('Location: /auth/reset-password?token=' . urlencode($token));
            exit;
        }
        
        if ($password !== $confirmPassword) {
            ErrorLog::log('Passwords do not match');
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: /auth/reset-password?token=' . urlencode($token));
            exit;
        }
        
        if (strlen($password) < 8) {
            ErrorLog::log('Password must be at least 8 characters long');
            $_SESSION['error'] = 'Password must be at least 8 characters long';
            header('Location: /auth/reset-password?token=' . urlencode($token));
            exit;
        }
        
        // Update user's password (email is already sanitized from the database)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updated = $this->userModel->updatePassword(
            Security::sanitizeOutput($reset['email']), 
            $hashedPassword
        );
        
        if ($updated) {
            // Delete the used token
            $this->resetModel->delete($token);
            
            // Clear session
            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_email']);
            
            $_SESSION['success'] = 'Your password has been reset successfully. You can now log in with your new password.';
            header('Location: /auth/login');
        } else {
            ErrorLog::log('Failed to reset password. Please try again.');
            $_SESSION['error'] = 'Failed to reset password. Please try again.';
            header('Location: /auth/reset-password?token=' . urlencode($token));
        }
        exit;
    }
    
    // Helper method to send password reset email using PHPMailer with .env config
    private function sendPasswordResetEmail($email, $token) {
        $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . '/auth/reset-password?token=' . urlencode($token);
        
        try {
            // Load environment variables if not loaded
            if (!isset($_ENV['MAIL_HOST'])) {
                $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
                $dotenv->load();
            }
            
            // Debug log environment variables
            ErrorLog::log("Mail configuration - " . json_encode([
                'host' => $_ENV['MAIL_HOST'] ?? 'not set',
                'port' => $_ENV['MAIL_PORT'] ?? 'not set',
                'username' => $_ENV['MAIL_USERNAME'] ? 'set' : 'not set',
                'from' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'not set'
            ]));
            
            if (empty($_ENV['MAIL_HOST']) || empty($_ENV['MAIL_FROM_ADDRESS'])) {
                throw new Exception('Mail configuration is incomplete. Please check your .env file.');
            }
            
            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
            $mail->Port = (int)$_ENV['MAIL_PORT'];
            
            // Enable debug
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) {
                ErrorLog::log("PHPMailer: $str");
            };
            
            // Recipients
            $fromAddress = $_ENV['MAIL_FROM_ADDRESS'];
            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'PocketPHP';
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($email);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <h2>Password Reset Request</h2>
                <p>You have requested to reset your password. Click the link below to set a new password:</p>
                <p><a href='$resetLink'>Reset Password</a></p>
                <p>Or copy and paste this link in your browser:</p>
                <p>$resetLink</p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request this, please ignore this email.</p>
            ";
            
            $mail->AltBody = "You have requested to reset your password. Please use the following link to reset your password: $resetLink\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.";
            
            if (!$mail->send()) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                return false;
            }
            return true;
            
        } catch (Exception $e) {
            // Log the error for debugging
            ErrorLog::log("Error sending password reset email: " . $e->getMessage());
            if (isset($mail)) {
                ErrorLog::log("PHPMailer Error Info: " . $mail->ErrorInfo);
            }
            return false;
        }
    }
}
