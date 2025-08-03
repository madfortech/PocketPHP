<?php

namespace PocketPHP\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $config;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->loadConfig();
        $this->configure();
    }

    private function loadConfig() {
        // Load environment variables from .env file if not already loaded
        if (file_exists(dirname(__DIR__, 2) . '/.env')) {
            $lines = file(dirname(__DIR__, 2) . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue; // Skip comments
                
                list($key, $value) = array_pad(explode('=', $line, 2), 2, null);
                $key = trim($key);
                $value = trim($value, "'\" \t\n\r\0\x0B");
                
                if ($key !== '' && $value !== null) {
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }

        // Helper function to safely get env values
        $getEnv = function($key, $default = '') {
            // Check in this order: $_ENV, $_SERVER, getenv()
            if (isset($_ENV[$key])) {
                return $_ENV[$key];
            } elseif (isset($_SERVER[$key])) {
                return $_SERVER[$key];
            }
            $value = getenv($key);
            return $value !== false ? $value : $default;
        };

        // Load configuration from environment variables
        $this->config = [
            'host' => $getEnv('MAIL_HOST'),
            'port' => (int)$getEnv('MAIL_PORT', '2525'),
            'username' => $getEnv('MAIL_USERNAME'),
            'password' => $getEnv('MAIL_PASSWORD'),
            'from_email' => $getEnv('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'from_name' => $getEnv('MAIL_FROM_NAME', 'PocketPHP'),
            'debug' => (bool)$getEnv('MAIL_DEBUG', '0')
        ];
        
        // Validate required configuration
        if (empty($this->config['host']) || empty($this->config['username']) || empty($this->config['password'])) {
            throw new \Exception('Email configuration is incomplete. Please check your .env file for MAIL_HOST, MAIL_USERNAME, and MAIL_PASSWORD.');
        }

        // Ensure no null values
        $this->config = array_map(function($value) {
            return $value === null ? '' : $value;
        }, $this->config);
        
        // Debug: Log the SMTP configuration (remove in production)
        error_log('SMTP Config: ' . print_r([
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'username' => $this->config['username'],
            'from' => $this->config['from_email']
        ], true));
    }

    private function configure() {
        try {
            // Validate required configuration
            if (empty($this->config['host']) || empty($this->config['username']) || empty($this->config['password'])) {
                throw new \Exception('Incomplete email configuration. Please check your .env file for MAIL_HOST, MAIL_USERNAME, and MAIL_PASSWORD.');
            }

            // Server settings
            $this->mailer->SMTPDebug = !empty($this->config['debug']) ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
            
            // Debug output handler
            $this->mailer->Debugoutput = function($str, $level) {
                if ($level >= SMTP::DEBUG_SERVER) {
                    error_log("PHPMailer ($level): $str");
                }
            };

            // Basic SMTP configuration
            $this->mailer->isSMTP();
            $this->mailer->Host = (string)$this->config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = (string)$this->config['username'];
            $this->mailer->Password = (string)$this->config['password'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = (int)$this->config['port'];
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
            
            // Set From address with fallback
            $fromEmail = !empty($this->config['from_email']) ? $this->config['from_email'] : 'noreply@' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'example.com');
            $this->mailer->setFrom($fromEmail, (string)$this->config['from_name']);
            
            // SMTP options for development
            $this->mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            // Test connection
            if (!$this->mailer->smtpConnect()) {
                throw new \Exception('SMTP connection failed');
            }
            $this->mailer->smtpClose();
            
        } catch (\Exception $e) {
            $error = "Mailer Configuration Error: " . $e->getMessage();
            error_log($error);
            throw new \Exception($error);
        }
    }

    public function sendVerificationEmail($toEmail, $toName, $verificationUrl) {
        try {
            // Clear all addresses from previous sends
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            // Recipients
            $this->mailer->addAddress($toEmail, $toName);
            
            // Content
            $this->mailer->isHTML(true);
            $subject = 'Verify Your Email Address';
            $this->mailer->Subject = $subject ?? '';
            
            // Email template
            $emailContent = $this->getVerificationEmailTemplate($toName, $verificationUrl);
            $this->mailer->Body = $emailContent ?? '';
            $this->mailer->AltBody = $emailContent ? strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $emailContent)) : '';
            
            // Log the email being sent
            error_log("Sending verification email to: " . $toEmail);
            
            // Send the email
            $result = $this->mailer->send();
            
            if (!$result) {
                throw new \Exception($this->mailer->ErrorInfo);
            }
            
            error_log("Verification email sent successfully to: " . $toEmail);
            return true;
            
        } catch (\Exception $e) {
            $error = "Failed to send verification email to {$toEmail}. Error: " . $e->getMessage();
            error_log($error);
            
            // Log the full error details for debugging
            error_log("PHPMailer Error: " . $this->mailer->ErrorInfo);
            
            // For development, you might want to log the full email content
            if ($this->config['debug']) {
                error_log("Email content: " . print_r([
                    'to' => $toEmail,
                    'subject' => 'Verify Your Email Address',
                    'verification_url' => $verificationUrl
                ], true));
            }
            
            return false;
        }
    }
    
    private function getVerificationEmailTemplate($name, $verificationUrl) {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #4a6baf; padding: 20px; color: white; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .button {
                        display: inline-block; 
                        padding: 10px 20px; 
                        margin: 20px 0; 
                        background-color: #4a6baf; 
                        color: white; 
                        text-decoration: none; 
                        border-radius: 4px;
                    }
                    .footer { 
                        margin-top: 20px; 
                        padding: 10px; 
                        text-align: center; 
                        font-size: 12px; 
                        color: #777; 
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Email Verification</h1>
                    </div>
                    <div class='content'>
                        <p>Hello {$name},</p>
                        <p>Thank you for registering with us. Please verify your email address by clicking the button below:</p>
                        <p style='text-align: center;'>
                            <a href='{$verificationUrl}' class='button'>Verify Email Address</a>
                        </p>
                        <p>Or copy and paste this link into your browser:</p>
                        <p><a href='{$verificationUrl}'>{$verificationUrl}</a></p>
                        <p>If you did not create an account, no further action is required.</p>
                        <p>Regards,<br>PocketPHP Team</p>
                    </div>
                    <div class='footer'>
                        &copy; " . date('Y') . " PocketPHP. All rights reserved.
                    </div>
                </div>
            </body>
            </html>
        ";
    }
}
