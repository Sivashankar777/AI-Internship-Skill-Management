<?php

class EmailService {
    private $fromEmail;
    private $fromName;
    
    public function __construct() {
        $this->fromEmail = defined('MAIL_FROM_EMAIL') ? MAIL_FROM_EMAIL : 'noreply@example.com';
        $this->fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : APP_NAME;
    }
    
    /**
     * Send an email
     */
    public function send($to, $subject, $message, $headers = []) {
        try {
            $defaultHeaders = [
                'From' => "{$this->fromName} <{$this->fromEmail}>",
                'Reply-To' => $this->fromEmail,
                'Content-Type' => 'text/html; charset=UTF-8',
                'MIME-Version' => '1.0'
            ];
            
            $headers = array_merge($defaultHeaders, $headers);
            $headerString = '';
            
            foreach ($headers as $key => $value) {
                $headerString .= "{$key}: {$value}\r\n";
            }
            
            // In production, use PHPMailer or similar library
            // For now, log the email instead of sending
            $this->logEmail($to, $subject, $message);
            
            // Uncomment for actual sending:
            // return mail($to, $subject, $message, $headerString);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Render an email template
     */
    public function renderTemplate($templateName, $data = []) {
        $templatePath = APP_ROOT . '/app/views/emails/' . $templateName . '.php';
        
        if (!file_exists($templatePath)) {
            // Return a basic template if file doesn't exist
            return $this->getDefaultTemplate($templateName, $data);
        }
        
        ob_start();
        extract($data);
        include $templatePath;
        return ob_get_clean();
    }
    
    /**
     * Get default email template
     */
    private function getDefaultTemplate($templateName, $data) {
        $appName = $data['app_name'] ?? APP_NAME;
        
        switch ($templateName) {
            case 'welcome':
                return "
                    <h2>Welcome to {$appName}!</h2>
                    <p>Hello {$data['name']},</p>
                    <p>Thank you for joining {$appName}. We're excited to have you!</p>
                    <p><a href='{$data['login_url']}'>Login to your account</a></p>
                ";
                
            case 'verification':
                return "
                    <h2>Verify Your Email</h2>
                    <p>Please click the link below to verify your email address:</p>
                    <p><a href='{$data['verification_url']}'>Verify Email</a></p>
                    <p>If you didn't create an account, please ignore this email.</p>
                ";
                
            case 'password_reset':
                return "
                    <h2>Password Reset Request</h2>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='{$data['reset_url']}'>Reset Password</a></p>
                    <p>This link will expire in 1 hour.</p>
                ";
                
            case 'password_reset_confirmation':
                return "
                    <h2>Password Reset Successful</h2>
                    <p>Your password has been successfully reset.</p>
                    <p><a href='{$data['login_url']}'>Login to your account</a></p>
                ";
                
            default:
                return "<p>" . implode('</p><p>', array_values($data)) . "</p>";
        }
    }
    
    /**
     * Log email for debugging
     */
    private function logEmail($to, $subject, $message) {
        $logDir = APP_ROOT . '/logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to,
            'subject' => $subject,
            'message_preview' => substr(strip_tags($message), 0, 100) . '...'
        ];
        
        file_put_contents(
            $logDir . '/emails.log',
            json_encode($logEntry) . PHP_EOL,
            FILE_APPEND
        );
    }
}
