<?php

class DashboardController {

    public function index() {

        // ✅ Start session safely
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 🔐 Protect dashboard
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // ✅ USE CORRECT SESSION KEYS
        $role = $_SESSION['user_role'];
        $username = $_SESSION['user_name'];

        // Optional: data array for views
        $data = [
            'username' => $username,
            'role' => $role
        ];

        // 🎯 Load role-based dashboard view
        $viewPath = APP_ROOT . '/app/views/dashboards/' . $role . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Dashboard for role '{$role}' not found.";
        }
    }
}
