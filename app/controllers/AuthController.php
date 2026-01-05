<?php

require_once APP_ROOT . '/app/models/User.php';
require_once APP_ROOT . '/app/services/EmailService.php';
require_once APP_ROOT . '/app/services/SessionService.php';

class AuthController {
    private $userModel;
    private $emailService;
    private $sessionService;

    public function __construct() {
        $this->userModel = new User();
        $this->emailService = new EmailService();
        $this->sessionService = new SessionService();
        
        // Initialize session if not already started
        $this->sessionService->start();
    }

    // SHOW REGISTER PAGE
    public function register() {
        // Check if user is already logged in
        if ($this->sessionService->isLoggedIn()) {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        // Pass any flash messages to view
        $error = $this->sessionService->getFlash('error');
        $success = $this->sessionService->getFlash('success');
        
        require APP_ROOT . '/app/views/auth/register.php';
    }

    // HANDLE REGISTER
    public function store() {
        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sessionService->setFlash('error', 'Invalid request method');
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken()) {
            $this->sessionService->setFlash('error', 'Invalid security token. Please try again.');
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        // Sanitize and validate input
        $data = $this->sanitizeRegistrationData($_POST);
        
        // Validate captcha if enabled
        if (defined('ENABLE_CAPTCHA') && ENABLE_CAPTCHA) {
            if (!$this->validateCaptcha()) {
                $this->sessionService->setFlash('error', 'Invalid captcha. Please try again.');
                $this->sessionService->setFlash('form_data', $data);
                header("Location: " . BASE_URL . "/register");
                exit;
            }
        }

        // Register user
        $result = $this->userModel->register($data);

        if ($result['success']) {
            // Send welcome email
            $this->sendWelcomeEmail($data['email'], $data['full_name']);
            
            // Send verification email if enabled
            if (defined('EMAIL_VERIFICATION') && EMAIL_VERIFICATION) {
                $this->sendVerificationEmail($data['email'], $result['verification_token']);
            }
            
            // Log registration
            $this->logRegistration($data['email'], $_SERVER['REMOTE_ADDR']);
            
            // Set success message
            $this->sessionService->setFlash('success', 'Registration successful! Please check your email to verify your account.');
            
            // Redirect to login with success parameter
            header("Location: " . BASE_URL . "/login?registered=1");
            exit;
        } else {
            // Store errors and form data in session
            $this->sessionService->setFlash('error', 'Registration failed. Please check the errors below.');
            $this->sessionService->setFlash('errors', $result['errors']);
            $this->sessionService->setFlash('form_data', $data);
            
            // Redirect back to register
            header("Location: " . BASE_URL . "/register");
            exit;
        }
    }

    // SHOW LOGIN PAGE
    public function login() {
        // Check if user is already logged in
        if ($this->sessionService->isLoggedIn()) {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        // Check for login attempt limit
        if ($this->isLoginRateLimited()) {
            $this->sessionService->setFlash('error', 'Too many login attempts. Please try again in 15 minutes.');
        }

        // Pass any flash messages to view
        $error = $this->sessionService->getFlash('error');
        $success = $this->sessionService->getFlash('success');
        $formData = $this->sessionService->getFlash('form_data');
        
        // Generate CSRF token for login form
        $csrfToken = $this->generateCSRFToken();
        
        require APP_ROOT . '/app/views/auth/login.php';
    }

    // HANDLE LOGIN
    public function authenticate() {
        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sessionService->setFlash('error', 'Invalid request method');
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken()) {
            $this->sessionService->setFlash('error', 'Invalid security token. Please try again.');
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // Check login attempt limit
        if ($this->isLoginRateLimited()) {
            $this->sessionService->setFlash('error', 'Too many login attempts. Please try again in 15 minutes.');
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // Sanitize input
        $identifier = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validate input
        if (empty($identifier) || empty($password)) {
            $this->recordFailedLogin($identifier);
            $this->sessionService->setFlash('error', 'Email and password are required');
            $this->sessionService->setFlash('form_data', ['email' => $identifier]);
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // Attempt login
        $result = $this->userModel->login($identifier, $password);

        if ($result['success']) {
            $user = $result['user'];
            
            // Check if account is verified
            if ($user['status'] === 'pending' && defined('EMAIL_VERIFICATION') && EMAIL_VERIFICATION) {
                $this->sessionService->setFlash('error', 'Please verify your email before logging in.');
                $this->sessionService->setFlash('form_data', ['email' => $identifier]);
                header("Location: " . BASE_URL . "/login");
                exit;
            }

            // Check if account is suspended
            if ($user['status'] === 'suspended') {
                $this->sessionService->setFlash('error', 'Your account has been suspended. Please contact support.');
                header("Location: " . BASE_URL . "/login");
                exit;
            }

            // Set session data
            $this->sessionService->set('user_id', $user['id']);
            $this->sessionService->set('user_name', $user['full_name']);
            $this->sessionService->set('user_email', $user['email']);
            $this->sessionService->set('user_role', $user['role']);
            $this->sessionService->set('user_avatar', $user['avatar'] ?? '');
            $this->sessionService->set('session_token', $result['session_token'] ?? '');
            $this->sessionService->set('last_login', date('Y-m-d H:i:s'));

            // Set remember me cookie if requested
            if ($remember) {
                $this->setRememberMeCookie($user['id']);
            }

            // Clear failed login attempts
            $this->clearFailedLogins($user['id']);

            // Log successful login
            $this->logLogin($user['id'], $_SERVER['REMOTE_ADDR']);

            // Check if there's a redirect URL
            $redirectUrl = $this->sessionService->get('redirect_url') ?? BASE_URL . "/dashboard";
            $this->sessionService->forget('redirect_url');

            // Redirect to intended page or dashboard
            header("Location: " . $redirectUrl);
            exit;
        } else {
            // Record failed login attempt
            $this->recordFailedLogin($identifier);
            
            // Set error message
            $this->sessionService->setFlash('error', $result['error']);
            $this->sessionService->setFlash('form_data', ['email' => $identifier]);
            
            // Redirect back to login
            header("Location: " . BASE_URL . "/login");
            exit;
        }
    }

    // VERIFY EMAIL
    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->sessionService->setFlash('error', 'Invalid verification link');
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $result = $this->userModel->verifyEmail($token);

        if ($result['success']) {
            $this->sessionService->setFlash('success', 'Email verified successfully! You can now login.');
            header("Location: " . BASE_URL . "/login");
            exit;
        } else {
            $this->sessionService->setFlash('error', $result['error']);
            header("Location: " . BASE_URL . "/login");
            exit;
        }
    }

    // FORGOT PASSWORD PAGE
    public function forgotPassword() {
        if ($this->sessionService->isLoggedIn()) {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $error = $this->sessionService->getFlash('error');
        $success = $this->sessionService->getFlash('success');
        
        require APP_ROOT . '/app/views/auth/forgot_password.php';
    }

    // SEND PASSWORD RESET EMAIL
    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sessionService->setFlash('error', 'Invalid request method');
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sessionService->setFlash('error', 'Please enter a valid email address');
            $this->sessionService->setFlash('form_data', ['email' => $email]);
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $result = $this->userModel->forgotPassword($email);

        if ($result['success']) {
            // Log password reset request
            $this->logPasswordResetRequest($email, $_SERVER['REMOTE_ADDR']);
            
            // Always show success message (for security, don't reveal if email exists)
            $this->sessionService->setFlash('success', 'If your email exists in our system, you will receive a password reset link.');
            header("Location: " . BASE_URL . "/login");
            exit;
        } else {
            $this->sessionService->setFlash('error', 'Failed to process your request. Please try again.');
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }
    }

    // RESET PASSWORD PAGE
    public function resetPassword() {
        if ($this->sessionService->isLoggedIn()) {
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->sessionService->setFlash('error', 'Invalid reset link');
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        // Validate token (check if exists and not expired)
        $isValidToken = $this->validateResetToken($token);

        if (!$isValidToken) {
            $this->sessionService->setFlash('error', 'Invalid or expired reset link');
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $error = $this->sessionService->getFlash('error');
        
        require APP_ROOT . '/app/views/auth/reset_password.php';
    }

    // HANDLE PASSWORD RESET
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sessionService->setFlash('error', 'Invalid request method');
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token)) {
            $this->sessionService->setFlash('error', 'Invalid reset token');
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        if ($password !== $confirmPassword) {
            $this->sessionService->setFlash('error', 'Passwords do not match');
            header("Location: " . BASE_URL . "/reset-password?token=" . urlencode($token));
            exit;
        }

        if (strlen($password) < 8) {
            $this->sessionService->setFlash('error', 'Password must be at least 8 characters long');
            header("Location: " . BASE_URL . "/reset-password?token=" . urlencode($token));
            exit;
        }

        // Validate token and get user
        $user = $this->getUserByResetToken($token);

        if (!$user) {
            $this->sessionService->setFlash('error', 'Invalid or expired reset token');
            header("Location: " . BASE_URL . "/forgot-password");
            exit;
        }

        // Update password
        $result = $this->userModel->changePassword($user['id'], '', $password);

        if ($result['success']) {
            // Clear reset token
            $this->clearResetToken($user['id']);
            
            // Log password reset
            $this->logPasswordReset($user['id'], $_SERVER['REMOTE_ADDR']);
            
            // Send confirmation email
            $this->sendPasswordResetConfirmation($user['email']);
            
            $this->sessionService->setFlash('success', 'Password reset successful! You can now login with your new password.');
            header("Location: " . BASE_URL . "/login");
            exit;
        } else {
            $this->sessionService->setFlash('error', $result['error']);
            header("Location: " . BASE_URL . "/reset-password?token=" . urlencode($token));
            exit;
        }
    }

    // LOGOUT
    public function logout() {
        // Log logout event
        if ($this->sessionService->isLoggedIn()) {
            $this->logLogout($this->sessionService->get('user_id'));
        }
        
        // Clear session
        $this->sessionService->destroy();
        
        // Clear remember me cookie
        $this->clearRememberMeCookie();
        
        // Redirect to login
        header("Location: " . BASE_URL . "/login?logged_out=1");
        exit;
    }

    // PRIVATE HELPER METHODS

    private function sanitizeRegistrationData($data) {
        return [
            'full_name'      => htmlspecialchars(trim($data['full_name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'username'       => htmlspecialchars(trim($data['username'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'email'          => filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'password'       => $data['password'] ?? '',
            'confirm_password' => $data['confirm_password'] ?? '',
            'role'           => htmlspecialchars(trim($data['role'] ?? 'intern'), ENT_QUOTES, 'UTF-8')
        ];
    }

    private function validateCSRFToken() {
        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($csrfToken) || $csrfToken !== $sessionToken) {
            return false;
        }
        
        // Clear token after use (one-time use)
        unset($_SESSION['csrf_token']);
        return true;
    }

    private function generateCSRFToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    private function validateCaptcha() {
        // Implement CAPTCHA validation (Google reCAPTCHA v3 recommended)
        $captchaResponse = $_POST['g-recaptcha-response'] ?? '';
        
        if (empty($captchaResponse)) {
            return false;
        }
        
        // Verify with Google
        $secretKey = defined('RECAPTCHA_SECRET_KEY') ? RECAPTCHA_SECRET_KEY : '';
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captchaResponse}");
        $responseData = json_decode($response);
        
        return $responseData->success ?? false;
    }

    private function isLoginRateLimited() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "login_attempts_{$ip}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 0,
                'timestamp' => time()
            ];
            return false;
        }
        
        $attempts = $_SESSION[$key];
        
        // Reset if 15 minutes have passed
        if (time() - $attempts['timestamp'] > 900) {
            $_SESSION[$key] = [
                'count' => 0,
                'timestamp' => time()
            ];
            return false;
        }
        
        // Check if exceeded limit (5 attempts)
        return $attempts['count'] >= 5;
    }

    private function recordFailedLogin($identifier) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "login_attempts_{$ip}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'timestamp' => time()
            ];
        } else {
            $_SESSION[$key]['count']++;
        }
        
        // Log failed login attempt in database
        $this->userModel->logFailedLogin($identifier, null);
    }

    private function clearFailedLogins($userId) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "login_attempts_{$ip}";
        unset($_SESSION[$key]);
        
        // Clear failed attempts in database
        $this->userModel->clearFailedAttempts($userId);
    }

    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Store token in database
        $this->userModel->storeRememberToken($userId, $token, date('Y-m-d H:i:s', $expiry));
        
        // Set cookie
        setcookie('remember_token', $token, $expiry, '/', '', true, true);
        setcookie('remember_user', $userId, $expiry, '/', '', true, true);
    }

    private function clearRememberMeCookie() {
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        setcookie('remember_user', '', time() - 3600, '/', '', true, true);
    }

    private function validateResetToken($token) {
        // Check token in database
        return $this->userModel->validateResetToken($token);
    }

    private function getUserByResetToken($token) {
        // Get user by reset token
        return $this->userModel->getUserByResetToken($token);
    }

    private function clearResetToken($userId) {
        // Clear reset token from database
        $this->userModel->clearResetToken($userId);
    }

    // LOGGING METHODS
    private function logRegistration($email, $ip) {
        $logMessage = sprintf(
            "[%s] Registration: %s from IP: %s",
            date('Y-m-d H:i:s'),
            $email,
            $ip
        );
        error_log($logMessage);
    }

    private function logLogin($userId, $ip) {
        $logMessage = sprintf(
            "[%s] Login: User ID %d from IP: %s",
            date('Y-m-d H:i:s'),
            $userId,
            $ip
        );
        error_log($logMessage);
    }

    private function logLogout($userId) {
        $logMessage = sprintf(
            "[%s] Logout: User ID %d",
            date('Y-m-d H:i:s'),
            $userId
        );
        error_log($logMessage);
    }

    private function logPasswordResetRequest($email, $ip) {
        $logMessage = sprintf(
            "[%s] Password Reset Request: %s from IP: %s",
            date('Y-m-d H:i:s'),
            $email,
            $ip
        );
        error_log($logMessage);
    }

    private function logPasswordReset($userId, $ip) {
        $logMessage = sprintf(
            "[%s] Password Reset: User ID %d from IP: %s",
            date('Y-m-d H:i:s'),
            $userId,
            $ip
        );
        error_log($logMessage);
    }

    // EMAIL METHODS
    private function sendWelcomeEmail($email, $name) {
        $subject = "Welcome to " . APP_NAME . "!";
        $message = $this->emailService->renderTemplate('welcome', [
            'name' => $name,
            'app_name' => APP_NAME,
            'login_url' => BASE_URL . '/login'
        ]);
        
        $this->emailService->send($email, $subject, $message);
    }

    private function sendVerificationEmail($email, $token) {
        $subject = "Verify Your Email - " . APP_NAME;
        $verificationUrl = BASE_URL . "/verify-email?token=" . $token;
        
        $message = $this->emailService->renderTemplate('verification', [
            'verification_url' => $verificationUrl,
            'app_name' => APP_NAME
        ]);
        
        $this->emailService->send($email, $subject, $message);
    }

    private function sendPasswordResetConfirmation($email) {
        $subject = "Password Reset Successful - " . APP_NAME;
        
        $message = $this->emailService->renderTemplate('password_reset_confirmation', [
            'app_name' => APP_NAME,
            'login_url' => BASE_URL . '/login'
        ]);
        
        $this->emailService->send($email, $subject, $message);
    }

    // AUTO-LOGIN VIA REMEMBER ME
    public function autoLogin() {
        if ($this->sessionService->isLoggedIn()) {
            return;
        }

        $rememberToken = $_COOKIE['remember_token'] ?? '';
        $userId = $_COOKIE['remember_user'] ?? '';

        if (!empty($rememberToken) && !empty($userId)) {
            $user = $this->userModel->validateRememberToken($userId, $rememberToken);
            
            if ($user) {
                $this->sessionService->set('user_id', $user['id']);
                $this->sessionService->set('user_name', $user['full_name']);
                $this->sessionService->set('user_email', $user['email']);
                $this->sessionService->set('user_role', $user['role']);
                $this->sessionService->set('last_login', date('Y-m-d H:i:s'));
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Redirect to dashboard
                header("Location: " . BASE_URL . "/dashboard");
                exit;
            }
        }
    }
}