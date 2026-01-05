<?php
if (!defined('APP_ROOT')) {
    exit('No direct script access allowed');
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | AI Internship Manager</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #1abc9c;
            --dark-color: #1a252f;
            --light-color: #ecf0f1;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Animation */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(52, 152, 219, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(26, 188, 156, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(44, 62, 80, 0.1) 0%, transparent 60%);
            z-index: -1;
            animation: pulse 20s ease-in-out infinite alternate;
        }

        @keyframes pulse {
            0% { opacity: 0.7; transform: scale(1); }
            100% { opacity: 1; transform: scale(1.05); }
        }

        /* Floating Elements */
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            z-index: -1;
            animation: float 25s infinite linear;
        }

        .floating-element:nth-child(1) {
            width: 250px;
            height: 250px;
            top: -125px;
            left: -125px;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 180px;
            height: 180px;
            bottom: -90px;
            right: -90px;
            animation-delay: -10s;
        }

        .floating-element:nth-child(3) {
            width: 120px;
            height: 120px;
            top: 20%;
            right: 15%;
            animation-delay: -5s;
        }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-30px) rotate(120deg); }
            66% { transform: translateY(20px) rotate(240deg); }
            100% { transform: translateY(0px) rotate(360deg); }
        }

        /* Glass Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px) saturate(180%);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 25px 45px rgba(0, 0, 0, 0.2),
                inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            color: var(--light-color);
            transition: all var(--transition-speed) ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 35px 60px rgba(0, 0, 0, 0.25),
                inset 0 0 0 1px rgba(255, 255, 255, 0.15);
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
            z-index: 2;
        }

        /* Form Elements */
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: var(--light-color);
            padding: 12px 15px;
            transition: all var(--transition-speed) ease;
            font-weight: 400;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.2);
            color: var(--light-color);
            outline: none;
        }

        .form-control::placeholder, .form-select::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-label {
            font-weight: 600;
            color: var(--light-color);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Role Select Styling */
        .form-select option {
            background: var(--dark-color);
            color: var(--light-color);
        }

        /* Password Strength */
        .password-strength {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all var(--transition-speed) ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.7s;
        }

        .btn-primary:hover::after {
            left: 100%;
        }

        /* Brand Title */
        .brand-title {
            font-weight: 700;
            font-size: 2rem;
            background: linear-gradient(90deg, var(--light-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-align: center;
            margin-bottom: 5px;
        }

        .brand-subtitle {
            font-weight: 400;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 30px;
            font-size: 1rem;
        }

        /* Role Cards */
        .role-option {
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.05);
        }

        .role-option:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .role-option.selected {
            background: rgba(26, 188, 156, 0.15);
            border-color: var(--accent-color);
        }

        .role-icon {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--accent-color);
        }

        /* Links */
        a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
            position: relative;
        }

        a:hover {
            color: var(--secondary-color);
        }

        a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--secondary-color);
            transition: width var(--transition-speed) ease;
        }

        a:hover::after {
            width: 100%;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 10px;
            font-weight: 500;
            backdrop-filter: blur(10px);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-warning {
            background: rgba(243, 156, 18, 0.2);
            color: #ffeaa7;
            border-left: 4px solid var(--warning-color);
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.2);
            color: #a8ffce;
            border-left: 4px solid var(--success-color);
        }

        /* Form Steps */
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 2;
            transition: all var(--transition-speed) ease;
        }

        .step.active {
            background: var(--accent-color);
            color: white;
            transform: scale(1.1);
        }

        .step.completed {
            background: var(--success-color);
            color: white;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: color var(--transition-speed) ease;
            z-index: 3;
        }

        .password-toggle:hover {
            color: var(--light-color);
        }

        .input-group {
            position: relative;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .glass-card {
                padding: 25px !important;
            }
            
            .brand-title {
                font-size: 1.8rem;
            }
            
            .step-indicator::before {
                left: 5%;
                right: 5%;
            }
        }

        /* Loading Animation */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-primary.loading .spinner {
            display: inline-block;
        }
    </style>
</head>

<body>
    <!-- Background floating elements -->
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="glass-card p-5">
                    <!-- Logo/Header -->
                    <div class="text-center mb-4">
                        <div class="brand-title mb-2">
                            <i class="bi bi-person-plus-fill me-2"></i>Create Account
                        </div>
                        <p class="brand-subtitle">Join AI Internship Manager Platform</p>
                    </div>

                    <!-- Step Indicator -->
                    <div class="step-indicator mb-5">
                        <div class="step active" data-step="1">1</div>
                        <div class="step" data-step="2">2</div>
                        <div class="step" data-step="3">3</div>
                    </div>

                    <!-- Messages -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Registration Form -->
                    <form method="POST" action="" id="registerForm">
                        
                        <!-- Step 1: Personal Information -->
                        <div class="form-step active" id="step1">
                            <h5 class="mb-4 fw-bold"><i class="bi bi-person-circle me-2"></i>Personal Information</h5>
                            
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-person-badge"></i> Full Name
                                </label>
                                <input type="text" name="full_name" class="form-control" 
                                       placeholder="Enter your full name" required 
                                       autocomplete="name">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-at"></i> Username
                                </label>
                                <input type="text" name="username" class="form-control" 
                                       placeholder="Choose a username" required
                                       autocomplete="username">
                                <div class="form-text text-white-50">Only letters, numbers, and underscores allowed</div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-light" disabled>Previous</button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                    Continue <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Account Details -->
                        <div class="form-step" id="step2">
                            <h5 class="mb-4 fw-bold"><i class="bi bi-envelope-check me-2"></i>Account Details</h5>
                            
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-envelope"></i> Email Address
                                </label>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="Enter your email" required
                                       autocomplete="email">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-shield-lock"></i> Choose Role
                                </label>
                                <div class="role-options">
                                    <div class="role-option" onclick="selectRole('intern')" data-role="intern">
                                        <div class="role-icon">
                                            <i class="bi bi-mortarboard"></i>
                                        </div>
                                        <h6 class="fw-bold mb-2">Intern / Student</h6>
                                        <p class="small text-white-50 mb-0">Looking for internship opportunities</p>
                                    </div>
                                    
                                    <div class="role-option" onclick="selectRole('mentor')" data-role="mentor">
                                        <div class="role-icon">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <h6 class="fw-bold mb-2">Mentor / Manager</h6>
                                        <p class="small text-white-50 mb-0">Guide and manage interns</p>
                                    </div>
                                    
                                    <div class="role-option" onclick="selectRole('admin')" data-role="admin">
                                        <div class="role-icon">
                                            <i class="bi bi-gear"></i>
                                        </div>
                                        <h6 class="fw-bold mb-2">Administrator</h6>
                                        <p class="small text-white-50 mb-0">Platform administration</p>
                                    </div>
                                </div>
                                <input type="hidden" name="role" id="selectedRole" required>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-light" onclick="prevStep(1)">
                                    <i class="bi bi-arrow-left me-2"></i> Back
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                    Continue <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Security -->
                        <div class="form-step" id="step3">
                            <h5 class="mb-4 fw-bold"><i class="bi bi-shield-check me-2"></i>Security</h5>
                            
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-key"></i> Password
                                </label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" 
                                           class="form-control" placeholder="Create a strong password" required
                                           autocomplete="new-password">
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength mt-2">
                                    <div class="password-strength-bar" id="passwordStrength"></div>
                                </div>
                                <div class="form-text text-white-50">
                                    Use at least 8 characters with uppercase, lowercase, and numbers
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-key-fill"></i> Confirm Password
                                </label>
                                <div class="input-group">
                                    <input type="password" name="confirm_password" id="confirm_password" 
                                           class="form-control" placeholder="Confirm your password" required
                                           autocomplete="new-password">
                                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text text-white-50" id="passwordMatch"></div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label small text-white-50" for="terms">
                                    I agree to the <a href="#" class="text-accent">Terms of Service</a> and 
                                    <a href="#" class="text-accent">Privacy Policy</a>
                                </label>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-light" onclick="prevStep(2)">
                                    <i class="bi bi-arrow-left me-2"></i> Back
                                </button>
                                <button type="submit" class="btn btn-primary" id="registerButton">
                                    <span class="spinner"></span>
                                    <i class="bi bi-person-plus me-2"></i> Create Account
                                </button>
                            </div>
                        </div>

                    </form>

                    <div class="text-center mt-4 pt-3 border-top border-secondary">
                        <p class="mb-0">
                            Already have an account?
                            <a href="<?= BASE_URL ?>/login" class="fw-semibold">Sign In</a>
                        </p>
                    </div>
                </div>

                <div class="text-center mt-4 text-white-50 small">
                    <p>&copy; <?php echo date('Y'); ?> AI Internship Manager. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentStep = 1;
        let selectedRole = '';

        document.addEventListener('DOMContentLoaded', function() {
            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('passwordStrength');
            
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                // Length check
                if (password.length >= 8) strength += 25;
                if (password.length >= 12) strength += 25;
                
                // Complexity checks
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 25;
                if (/[^A-Za-z0-9]/.test(password)) strength += 25;
                
                // Cap at 100
                strength = Math.min(strength, 100);
                
                // Update bar
                strengthBar.style.width = strength + '%';
                
                // Update color
                if (strength < 40) {
                    strengthBar.style.backgroundColor = '#e74c3c';
                } else if (strength < 70) {
                    strengthBar.style.backgroundColor = '#f39c12';
                } else {
                    strengthBar.style.backgroundColor = '#27ae60';
                }
            });

            // Password confirmation check
            const confirmInput = document.getElementById('confirm_password');
            const matchText = document.getElementById('passwordMatch');
            
            confirmInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirm = this.value;
                
                if (confirm === '') {
                    matchText.textContent = '';
                } else if (password === confirm) {
                    matchText.textContent = '✓ Passwords match';
                    matchText.style.color = '#27ae60';
                } else {
                    matchText.textContent = '✗ Passwords do not match';
                    matchText.style.color = '#e74c3c';
                }
            });

            // Form submission
            const registerForm = document.getElementById('registerForm');
            const registerButton = document.getElementById('registerButton');
            
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate role selection
                if (!selectedRole) {
                    alert('Please select a role');
                    return;
                }
                
                // Validate terms
                if (!document.getElementById('terms').checked) {
                    alert('Please accept the terms and conditions');
                    return;
                }
                
                // Validate password match
                if (passwordInput.value !== confirmInput.value) {
                    alert('Passwords do not match');
                    return;
                }
                
                // Show loading state
                registerButton.classList.add('loading');
                registerButton.disabled = true;
                
                // Submit form after delay (simulating API call)
                setTimeout(() => {
                    this.submit();
                }, 1500);
            });
        });

        function nextStep(step) {
            // Validate current step
            if (!validateStep(currentStep)) return;
            
            // Hide current step
            document.getElementById(`step${currentStep}`).classList.remove('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
            
            // Show next step
            currentStep = step;
            document.getElementById(`step${currentStep}`).classList.add('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
            
            // Update step indicators
            for (let i = 1; i < currentStep; i++) {
                document.querySelector(`.step[data-step="${i}"]`).classList.add('completed');
            }
        }

        function prevStep(step) {
            // Hide current step
            document.getElementById(`step${currentStep}`).classList.remove('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
            
            // Show previous step
            currentStep = step;
            document.getElementById(`step${currentStep}`).classList.add('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
        }

        function validateStep(step) {
            const inputs = document.querySelectorAll(`#step${step} input[required]`);
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        }

        function selectRole(role) {
            selectedRole = role;
            document.getElementById('selectedRole').value = role;
            
            // Update visual selection
            document.querySelectorAll('.role-option').forEach(option => {
                option.classList.remove('selected');
            });
            document.querySelector(`.role-option[data-role="${role}"]`).classList.add('selected');
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
            
            const icon = field.nextElementSibling.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        }

        // Form input animations
        const inputs = document.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Animate card on load
        const card = document.querySelector('.glass-card');
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    </script>
</body>
</html>