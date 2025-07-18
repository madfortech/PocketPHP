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
        // Load configuration from environment variables
        $this->config = [
            'host' => 'sandbox.smtp.mailtrap.io',
            'port' => 2525,
            'username' => 'cc07721ae2f4af',
            'password' => 'd55151300c4738',
            'from_email' => 'from@example.com',
            'from_name' => 'PocketPHP',
            'debug' => true
        ];
        
        // Override with .env values if they exist
        if (getenv('MAIL_HOST')) $this->config['host'] = getenv('MAIL_HOST');
        if (getenv('MAIL_PORT')) $this->config['port'] = (int)getenv('MAIL_PORT');
        if (getenv('MAIL_USERNAME')) $this->config['username'] = getenv('MAIL_USERNAME');
        if (getenv('MAIL_PASSWORD')) $this->config['password'] = getenv('MAIL_PASSWORD');
        if (getenv('MAIL_FROM_ADDRESS')) $this->config['from_email'] = getenv('MAIL_FROM_ADDRESS');
        if (getenv('MAIL_FROM_NAME')) $this->config['from_name'] = getenv('MAIL_FROM_NAME');
        if (getenv('MAIL_DEBUG') !== false) $this->config['debug'] = (bool)getenv('MAIL_DEBUG');
        
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
            // Server settings
            $this->mailer->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output
            
            // Only log errors to error log, don't display to user
            $this->mailer->Debugoutput = function($str, $level) {
                if ($level >= SMTP::DEBUG_SERVER) {
                    error_log("PHPMailer ($level): $str");
                }
            };
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->config['port'];
            $this->mailer->CharSet = 'UTF-8';
            
            // Debug output
            $this->mailer->Debugoutput = function($str, $level) {
                error_log("PHPMailer ($level): $str");
            };
            
            // From
            $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
            
            // For Mailtrap, disable TLS certificate verification
            $this->mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Mailer Configuration Error: " . $e->getMessage());
            throw new \Exception("Email configuration failed: " . $e->getMessage());
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
            $this->mailer->Subject = 'Verify Your Email Address';
            
            // Email template
            $emailContent = $this->getVerificationEmailTemplate($toName, $verificationUrl);
            $this->mailer->Body = $emailContent;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $emailContent));
            
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
