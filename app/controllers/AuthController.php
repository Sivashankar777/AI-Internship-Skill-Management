<?php

require_once APP_ROOT . '/app/models/User.php';

class AuthController {

    // SHOW REGISTER PAGE
    public function register() {
        require APP_ROOT . '/app/views/auth/register.php';
    }

    // HANDLE REGISTER
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/register");
            exit;
        }

        $data = [
            'full_name'      => $_POST['full_name'] ?? '',
            'username'       => $_POST['username'] ?? '',
            'email'          => $_POST['email'] ?? '',
            'password'       => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'role'           => $_POST['role'] ?? 'intern'
        ];

        $userModel = new User();
        $result = $userModel->register($data);

        if ($result['success']) {
            header("Location: " . BASE_URL . "/login?registered=1");
            exit;
        } else {
            // In a real app, you would pass errors back to the view
            // For now, let's display them
            echo "Registration failed:<br>";
            foreach ($result['errors'] as $error) {
                echo "- $error<br>";
            }
            exit;
        }
    }

    // SHOW LOGIN PAGE
    public function login() {
        require APP_ROOT . '/app/views/auth/login.php';
    }

    // HANDLE LOGIN
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $identifier = $_POST['email'] ?? '';
        $password   = $_POST['password'] ?? '';

        $userModel = new User();
        $result = $userModel->login($identifier, $password);

        if ($result['success']) {
            $user = $result['user'];
            session_start();
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            header("Location: " . BASE_URL . "/dashboard");
            exit;
        } else {
            // Pass error to login view? For now, die with error
            die($result['error']); 
        }
    }

    // LOGOUT
    public function logout() {
        session_start();
        session_destroy();
        header("Location: " . BASE_URL . "/login");
        exit;
    }
}
