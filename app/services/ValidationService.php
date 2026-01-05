<?php

class ValidationService {
    private $errors = [];
    
    /**
     * Validate required field
     */
    public function required($value, $field) {
        if (empty(trim($value))) {
            $this->errors[$field] = ucfirst($field) . ' is required';
            return false;
        }
        return true;
    }
    
    /**
     * Validate email
     */
    public function email($value, $field = 'email') {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Invalid email address';
            return false;
        }
        return true;
    }
    
    /**
     * Validate minimum length
     */
    public function minLength($value, $min, $field) {
        if (strlen($value) < $min) {
            $this->errors[$field] = ucfirst($field) . " must be at least {$min} characters";
            return false;
        }
        return true;
    }
    
    /**
     * Validate maximum length
     */
    public function maxLength($value, $max, $field) {
        if (strlen($value) > $max) {
            $this->errors[$field] = ucfirst($field) . " cannot exceed {$max} characters";
            return false;
        }
        return true;
    }
    
    /**
     * Validate password match
     */
    public function matches($value1, $value2, $field = 'password') {
        if ($value1 !== $value2) {
            $this->errors[$field] = 'Passwords do not match';
            return false;
        }
        return true;
    }
    
    /**
     * Validate numeric value
     */
    public function numeric($value, $field) {
        if (!is_numeric($value)) {
            $this->errors[$field] = ucfirst($field) . ' must be a number';
            return false;
        }
        return true;
    }
    
    /**
     * Validate date format
     */
    public function date($value, $format = 'Y-m-d', $field = 'date') {
        $date = DateTime::createFromFormat($format, $value);
        if (!$date || $date->format($format) !== $value) {
            $this->errors[$field] = 'Invalid date format';
            return false;
        }
        return true;
    }
    
    /**
     * Validate future date
     */
    public function futureDate($value, $field = 'date') {
        $date = DateTime::createFromFormat('Y-m-d', $value);
        $today = new DateTime();
        
        if (!$date || $date <= $today) {
            $this->errors[$field] = ucfirst($field) . ' must be a future date';
            return false;
        }
        return true;
    }
    
    /**
     * Validate in array
     */
    public function inArray($value, $array, $field) {
        if (!in_array($value, $array)) {
            $this->errors[$field] = 'Invalid ' . $field . ' selected';
            return false;
        }
        return true;
    }
    
    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Check if has errors
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Clear errors
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * Sanitize string
     */
    public function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize email
     */
    public function sanitizeEmail($value) {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }
}
