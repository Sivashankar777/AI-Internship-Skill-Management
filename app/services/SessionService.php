<?php

class SessionService {
    
    /**
     * Start session if not already started
     */
    public function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get a session value
     */
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Set a session value
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Remove a session value
     */
    public function forget($key) {
        unset($_SESSION[$key]);
    }
    
    /**
     * Check if session has a key
     */
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Set a flash message (one-time message)
     */
    public function setFlash($key, $value) {
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Get and remove a flash message
     */
    public function getFlash($key, $default = null) {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    
    /**
     * Check if flash message exists
     */
    public function hasFlash($key) {
        return isset($_SESSION['_flash'][$key]);
    }
    
    /**
     * Get all flash messages
     */
    public function getAllFlash() {
        $flash = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flash;
    }
    
    /**
     * Destroy the session
     */
    public function destroy() {
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }
    
    /**
     * Regenerate session ID
     */
    public function regenerate($deleteOld = true) {
        session_regenerate_id($deleteOld);
    }
    
    /**
     * Get all session data
     */
    public function all() {
        return $_SESSION;
    }
}
