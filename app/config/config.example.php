<?php

// Environment Detection
$is_local = (
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    $_SERVER['REMOTE_ADDR'] === '127.0.0.1'
);

if ($is_local) {
    // 🔹 LOCAL (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'your_database_name');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('BASE_URL', 'http://localhost/internship_ai_dev');

} else {
    // 🔹 PRODUCTION (iPage / Server)
    define('DB_HOST', 'your_db_host');
    define('DB_NAME', 'your_db_name');
    define('DB_USER', 'your_db_user');
    define('DB_PASS', 'your_db_password');
    define('BASE_URL', 'https://yourdomain.com');
}

define('APP_NAME', 'AI Internship Manager');
define('APP_ROOT', dirname(dirname(__DIR__)));

// Error Reporting
if ($is_local) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Secure Session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 3600);
session_start();

// AI Configuration
define('AI_PROVIDER', 'gemini');
define('AI_API_KEY', 'YOUR_API_KEY_HERE');
