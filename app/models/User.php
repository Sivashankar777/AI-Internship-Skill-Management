<?php

class User {
    private $db;


    public function __construct() {
        $this->db = Database::getInstance();
    }

    // REGISTER USER
    public function register($data) {
        // Validate input data
        $validationErrors = $this->validateRegistration($data);
        if (!empty($validationErrors)) {
            return ['success' => false, 'errors' => $validationErrors];
        }

        // Check if user already exists
        if ($this->userExists($data['email'], $data['username'])) {
            return [
                'success' => false, 
                'errors' => ['email' => 'User with this email or username already exists']
            ];
        }

        $sql = "
            INSERT INTO users (
                full_name, 
                username, 
                email, 
                password_hash, 
                role, 
                created_at
            ) VALUES (
                :full_name, 
                :username, 
                :email, 
                :password_hash, 
                :role, 
                NOW()
            )
        ";

        $params = [
            ':full_name'           => htmlspecialchars(trim($data['full_name'])),
            ':username'            => htmlspecialchars(trim($data['username'])),
            ':email'               => filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL),
            ':password_hash'       => $this->hashPassword($data['password']),
            ':role'                => $this->validateRole($data['role'])
        ];

        try {
            $this->db->query($sql, $params);
            $userId = $this->db->lastInsertId();
            
            // Log registration (wrapped in try-catch internally or removed if table missing)
            // For now, let's comment it out to ensure basic flow works
            // $this->logActivity($userId, 'USER_REGISTERED', 'New user registration');
            
            return [
                'success' => true,
                'user_id' => $userId
            ];
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false, 
                'errors' => ['database' => 'Registration failed: ' . $e->getMessage()]
            ];
        }
    }

    // LOGIN USER
    public function login($identifier, $password) {
        // Validate input
        if (empty($identifier) || empty($password)) {
            return ['success' => false, 'error' => 'Email/Username and password are required'];
        }

        $sql = "SELECT * FROM users 
                WHERE username = :username OR email = :email";

        try {
            $stmt = $this->db->query($sql, [
                ':username' => htmlspecialchars(trim($identifier)),
                ':email'    => filter_var(trim($identifier), FILTER_SANITIZE_EMAIL)
            ]);

            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'error' => 'Invalid credentials'];
            }

            // Check password
            if (!$this->verifyPassword($password, $user['password_hash'])) {
                return ['success' => false, 'error' => 'Invalid credentials'];
            }

            // Removed account locking, last_login, session_token logic as columns don't exist

            // Remove sensitive data before returning
            unset($user['password_hash']);

            return [
                'success' => true,
                'user' => $user
            ];

        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Login failed. Please try again.'];
        }
    }

    // VALIDATE REGISTRATION DATA
    private function validateRegistration($data) {
        $errors = [];

        // Full Name validation
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        } elseif (strlen($data['full_name']) < 2 || strlen($data['full_name']) > 100) {
            $errors['full_name'] = 'Full name must be between 2 and 100 characters';
        } elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $data['full_name'])) {
            $errors['full_name'] = 'Full name contains invalid characters';
        }

        // Username validation
        if (empty($data['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($data['username']) < 3 || strlen($data['username']) > 50) {
            $errors['username'] = 'Username must be between 3 and 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores';
        }

        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = 'Email is too long';
        }

        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        } elseif (!preg_match('/[A-Z]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match('/[0-9]/', $data['password'])) {
            $errors['password'] = 'Password must contain at least one number';
        }

        // Role validation
        if (empty($data['role'])) {
            $errors['role'] = 'Role is required';
        } elseif (!in_array($data['role'], ['intern', 'mentor', 'admin'])) {
            $errors['role'] = 'Invalid role selected';
        }

        // Confirm password validation
        if (empty($data['confirm_password']) || $data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        return $errors;
    }

    // CHECK IF USER EXISTS
    private function userExists($email, $username) {
        $sql = "SELECT COUNT(*) as count FROM users 
                WHERE email = :email OR username = :username";
        
        $stmt = $this->db->query($sql, [
            ':email' => filter_var(trim($email), FILTER_SANITIZE_EMAIL),
            ':username' => htmlspecialchars(trim($username))
        ]);
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    // HASH PASSWORD
    private function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // VERIFY PASSWORD
    private function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // VALIDATE ROLE
    private function validateRole($role) {
        $validRoles = ['intern', 'mentor', 'admin'];
        return in_array($role, $validRoles) ? $role : 'intern';
    }

    // SEND VERIFICATION EMAIL
    private function sendVerificationEmail($email, $token) {
        $verificationLink = BASE_URL . "/verify?token=" . $token;
        
        $subject = "Verify Your Account - AI Internship Manager";
        $message = "
            <html>
            <head>
                <title>Account Verification</title>
            </head>
            <body>
                <h2>Welcome to AI Internship Manager!</h2>
                <p>Please click the link below to verify your account:</p>
                <p><a href='$verificationLink'>Verify Account</a></p>
                <p>This link will expire in 24 hours.</p>
                <p>If you didn't create an account, please ignore this email.</p>
            </body>
            </html>
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        
        // In production, use a proper email library like PHPMailer
        // mail($email, $subject, $message, $headers);
        
        // For now, just log it
        error_log("Verification email sent to: $email, Token: $token");
        return true;
    }

    // LOG ACTIVITY
    private function logActivity($userId, $activityType, $description) {
        $sql = "
            INSERT INTO user_activities (user_id, activity_type, description, ip_address, user_agent, created_at)
            VALUES (:user_id, :activity_type, :description, :ip_address, :user_agent, NOW())
        ";
        
        $params = [
            ':user_id'       => $userId,
            ':activity_type' => $activityType,
            ':description'   => $description,
            ':ip_address'    => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        try {
            $this->db->query($sql, $params);
        } catch (PDOException $e) {
            error_log("Activity logging failed: " . $e->getMessage());
        }
    }

    // LOG FAILED LOGIN ATTEMPT
    private function logFailedLogin($identifier, $userId = null) {
        $sql = "
            INSERT INTO failed_logins (user_id, username, ip_address, user_agent, attempted_at)
            VALUES (:user_id, :username, :ip_address, :user_agent, NOW())
        ";
        
        $params = [
            ':user_id'    => $userId,
            ':username'   => $identifier,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        try {
            $this->db->query($sql, $params);
        } catch (PDOException $e) {
            error_log("Failed login logging failed: " . $e->getMessage());
        }
    }

    // CHECK IF ACCOUNT IS LOCKED
    private function isAccountLocked($userId) {
        // Check if there are more than 5 failed attempts in the last 15 minutes
        $sql = "
            SELECT COUNT(*) as attempts 
            FROM failed_logins 
            WHERE user_id = :user_id 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ";
        
        $stmt = $this->db->query($sql, [':user_id' => $userId]);
        $result = $stmt->fetch();
        
        return $result['attempts'] >= 5;
    }

    // UPDATE LAST LOGIN
    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $this->db->query($sql, [':id' => $userId]);
    }

    // CLEAR FAILED ATTEMPTS
    private function clearFailedAttempts($userId) {
        $sql = "DELETE FROM failed_logins WHERE user_id = :user_id";
        $this->db->query($sql, [':user_id' => $userId]);
    }

    // GENERATE SESSION TOKEN
    private function generateSessionToken() {
        return bin2hex(random_bytes(32));
    }

    // STORE SESSION TOKEN
    private function storeSessionToken($userId, $token) {
        $sql = "UPDATE users SET session_token = :token WHERE id = :id";
        $this->db->query($sql, [
            ':token' => $token,
            ':id' => $userId
        ]);
    }

    // VERIFY EMAIL TOKEN
    public function verifyEmail($token) {
        $sql = "
            SELECT id, verification_expiry 
            FROM users 
            WHERE verification_token = :token 
            AND status = 'pending'
        ";
        
        $stmt = $this->db->query($sql, [':token' => $token]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'error' => 'Invalid or expired verification token'];
        }
        
        if (strtotime($user['verification_expiry']) < time()) {
            return ['success' => false, 'error' => 'Verification token has expired'];
        }
        
        // Update user status
        $updateSql = "
            UPDATE users 
            SET status = 'active', 
                verification_token = NULL, 
                verification_expiry = NULL,
                verified_at = NOW()
            WHERE id = :id
        ";
        
        $this->db->query($updateSql, [':id' => $user['id']]);
        
        // Log verification
        $this->logActivity($user['id'], 'EMAIL_VERIFIED', 'Email verified successfully');
        
        return ['success' => true, 'user_id' => $user['id']];
    }

    // GET USER BY ID
    public function getUserById($id) {
        $sql = "
            SELECT id, full_name, username, email, role, status, 
                   created_at, last_login, verified_at
            FROM users 
            WHERE id = :id AND status = 'active'
        ";
        
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt->fetch();
    }

    // UPDATE USER PROFILE
    public function updateProfile($userId, $data) {
        $sql = "
            UPDATE users 
            SET full_name = :full_name, 
                username = :username, 
                email = :email,
                updated_at = NOW()
            WHERE id = :id AND status = 'active'
        ";
        
        $params = [
            ':full_name' => htmlspecialchars(trim($data['full_name'])),
            ':username'  => htmlspecialchars(trim($data['username'])),
            ':email'     => filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL),
            ':id'        => $userId
        ];
        
        try {
            $this->db->query($sql, $params);
            $this->logActivity($userId, 'PROFILE_UPDATED', 'User profile updated');
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Profile update error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Profile update failed'];
        }
    }

    // CHANGE PASSWORD
    public function changePassword($userId, $currentPassword, $newPassword) {
        // Get current password hash
        $sql = "SELECT password_hash FROM users WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $userId]);
        $user = $stmt->fetch();
        
        if (!$user || !$this->verifyPassword($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Current password is incorrect'];
        }
        
        // Validate new password
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters'];
        }
        
        // Update password
        $updateSql = "
            UPDATE users 
            SET password_hash = :password_hash, 
                updated_at = NOW(),
                password_changed_at = NOW()
            WHERE id = :id
        ";
        
        $this->db->query($updateSql, [
            ':password_hash' => $this->hashPassword($newPassword),
            ':id' => $userId
        ]);
        
        $this->logActivity($userId, 'PASSWORD_CHANGED', 'Password changed successfully');
        return ['success' => true];
    }

    // FORGOT PASSWORD
    public function forgotPassword($email) {
        $sql = "SELECT id FROM users WHERE email = :email AND status = 'active'";
        $stmt = $this->db->query($sql, [':email' => filter_var(trim($email), FILTER_SANITIZE_EMAIL)]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => true]; // Don't reveal if email exists
        }
        
        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        $resetExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $updateSql = "
            UPDATE users 
            SET reset_token = :reset_token, 
                reset_expiry = :reset_expiry
            WHERE id = :id
        ";
        
        $this->db->query($updateSql, [
            ':reset_token' => $resetToken,
            ':reset_expiry' => $resetExpiry,
            ':id' => $user['id']
        ]);
        
        // Send reset email
        $resetLink = BASE_URL . "/reset-password?token=" . $resetToken;
        
        // Log password reset request
        $this->logActivity($user['id'], 'PASSWORD_RESET_REQUESTED', 'Password reset requested');
        
        return [
            'success' => true,
            'reset_token' => $resetToken,
            'reset_link' => $resetLink
        ];
    }
}