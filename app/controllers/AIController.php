<?php

require_once APP_ROOT . '/app/services/AIService.php';

class AIController {
    private $aiService;
    
    public function __construct() {
        $this->aiService = new AIService();
        $this->init();
    }
    
    private function init() {
        // Enable CORS if needed
        $this->enableCORS();
        
        // Set JSON header
        header('Content-Type: application/json');
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
    
    private function enableCORS() {
        // Allow from any origin (adjust in production)
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400'); // Cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            
            exit(0);
        }
    }
    
    public function analyze() {
        try {
            // Validate request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendError('Method not allowed', 405);
                return;
            }
            
            // Get and validate input
            $input = $this->getValidatedInput();
            $type = $input['type'] ?? 'skill_gap';
            $userId = $_SESSION['user_id'] ?? null;
            
            // Rate limiting check (optional)
            if (!$this->checkRateLimit($userId)) {
                $this->sendError('Rate limit exceeded. Please try again later.', 429);
                return;
            }
            
            // Process based on type
            $response = $this->processRequest($type, $input, $userId);
            
            // Log successful request
            $this->logRequest($type, $userId, 'success');
            
            // Send response
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            error_log("AI Controller Error: " . $e->getMessage());
            $this->sendError('An internal error occurred', 500);
        }
    }
    
    public function skillGap() {
        $this->handleSpecificRequest('skill_gap');
    }
    
    public function resumeReview() {
        $this->handleSpecificRequest('resume_review');
    }
    
    public function interviewQuestions() {
        $this->handleSpecificRequest('interview_questions');
    }
    
    public function taskFeedback() {
        $this->handleSpecificRequest('task_feedback');
    }
    
    public function health() {
        // Health check endpoint
        $response = [
            'status' => 'healthy',
            'service' => 'AI Internship Manager',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0'
        ];
        
        echo json_encode($response);
    }
    
    private function handleSpecificRequest($type) {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendError('Method not allowed', 405);
                return;
            }
            
            $input = $this->getValidatedInput();
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$this->checkRateLimit($userId)) {
                $this->sendError('Rate limit exceeded', 429);
                return;
            }
            
            $response = $this->processRequest($type, $input, $userId);
            $this->logRequest($type, $userId, 'success');
            
            echo json_encode($response, JSON_PRETTY_PRINT);
            
        } catch (Exception $e) {
            error_log("{$type} Request Error: " . $e->getMessage());
            $this->sendError('Processing failed', 500);
        }
    }
    
    private function getValidatedInput() {
        $rawInput = file_get_contents('php://input');
        
        // Check if input is empty
        if (empty($rawInput)) {
            $this->sendError('Empty request body', 400);
        }
        
        $input = json_decode($rawInput, true);
        
        // Validate JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendError('Invalid JSON format', 400);
        }
        
        // Sanitize input
        return $this->sanitizeInput($input);
    }
    
    private function sanitizeInput($input) {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = array_map(function($item) {
                    return is_string($item) ? htmlspecialchars(trim($item), ENT_QUOTES, 'UTF-8') : $item;
                }, $value);
            } elseif (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    private function processRequest($type, $input, $userId = null) {
        switch ($type) {
            case 'skill_gap':
                return $this->processSkillGap($input, $userId);
                
            case 'resume_review':
                return $this->processResumeReview($input, $userId);
                
            case 'interview_questions':
                return $this->processInterviewQuestions($input, $userId);
                
            case 'task_feedback':
                return $this->processTaskFeedback($input, $userId);
                
            default:
                $this->sendError('Invalid request type', 400);
        }
    }
    
    private function processSkillGap($input, $userId) {
        // Validate required parameters
        if (empty($input['user_skills'])) {
            $this->sendError('User skills are required', 400);
        }
        
        $userSkills = is_array($input['user_skills']) ? $input['user_skills'] : explode(',', $input['user_skills']);
        $requiredSkills = $input['required_skills'] ?? [];
        
        if (!empty($requiredSkills) && !is_array($requiredSkills)) {
            $requiredSkills = explode(',', $requiredSkills);
        }
        
        // Clean and validate skills
        $userSkills = array_map('trim', array_filter($userSkills));
        $requiredSkills = array_map('trim', array_filter($requiredSkills));
        
        if (empty($userSkills)) {
            $this->sendError('User skills cannot be empty', 400);
        }
        
        // Call AI service
        return $this->aiService->analyzeSkillGap($userSkills, $requiredSkills, $userId);
    }
    
    private function processResumeReview($input, $userId) {
        // Validate required parameters
        if (empty($input['resume_text'])) {
            $this->sendError('Resume text is required', 400);
        }
        
        $resumeText = $input['resume_text'];
        
        // Validate resume text length
        if (strlen(trim($resumeText)) < 50) {
            $this->sendError('Resume text is too short. Minimum 50 characters required.', 400);
        }
        
        if (strlen($resumeText) > 10000) {
            $this->sendError('Resume text is too long. Maximum 10000 characters allowed.', 400);
        }
        
        $targetRole = $input['target_role'] ?? null;
        
        // Call AI service
        return $this->aiService->reviewResume($resumeText, $targetRole, $userId);
    }
    
    private function processInterviewQuestions($input, $userId) {
        // Validate required parameters
        if (empty($input['skills'])) {
            $this->sendError('Skills are required', 400);
        }
        
        $skills = is_array($input['skills']) ? $input['skills'] : explode(',', $input['skills']);
        $difficulty = $input['difficulty'] ?? 'intermediate';
        
        // Validate difficulty level
        $validDifficulties = ['beginner', 'intermediate', 'advanced', 'expert'];
        if (!in_array($difficulty, $validDifficulties)) {
            $difficulty = 'intermediate';
        }
        
        // Clean and validate skills
        $skills = array_map('trim', array_filter($skills));
        
        if (empty($skills)) {
            $this->sendError('Skills cannot be empty', 400);
        }
        
        // Call AI service
        return $this->aiService->generateInterviewQuestions($skills, $difficulty, $userId);
    }
    
    private function processTaskFeedback($input, $userId) {
        // Validate required parameters
        if (empty($input['task_description'])) {
            $this->sendError('Task description is required', 400);
        }
        
        if (empty($input['submission_text'])) {
            $this->sendError('Submission text is required', 400);
        }
        
        $taskDescription = $input['task_description'];
        $submissionText = $input['submission_text'];
        
        // Validate lengths
        if (strlen(trim($taskDescription)) < 10) {
            $this->sendError('Task description is too short', 400);
        }
        
        if (strlen(trim($submissionText)) < 10) {
            $this->sendError('Submission text is too short', 400);
        }
        
        // Call AI service
        return $this->aiService->analyzeTaskSubmission($taskDescription, $submissionText, $userId);
    }
    
    private function checkRateLimit($userId) {
        // Skip rate limiting for non-authenticated users (or adjust as needed)
        if (!$userId) {
            return true;
        }
        
        // Implement rate limiting logic
        // Example: 10 requests per minute per user
        $key = "rate_limit_{$userId}";
        $currentTime = time();
        $window = 60; // 1 minute in seconds
        $maxRequests = 10;
        
        // This is a simplified implementation
        // In production, use Redis or database for rate limiting
        
        if (!isset($_SESSION['rate_limits'][$key])) {
            $_SESSION['rate_limits'][$key] = [
                'count' => 1,
                'timestamp' => $currentTime
            ];
            return true;
        }
        
        $rateData = $_SESSION['rate_limits'][$key];
        
        if ($currentTime - $rateData['timestamp'] > $window) {
            // Reset counter if window has passed
            $_SESSION['rate_limits'][$key] = [
                'count' => 1,
                'timestamp' => $currentTime
            ];
            return true;
        }
        
        if ($rateData['count'] >= $maxRequests) {
            return false;
        }
        
        // Increment counter
        $_SESSION['rate_limits'][$key]['count']++;
        return true;
    }
    
    private function sendError($message, $code = 400) {
        http_response_code($code);
        
        $response = [
            'status' => 'error',
            'error_code' => $this->getErrorCode($code),
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response);
        exit();
    }
    
    private function getErrorCode($httpCode) {
        $codes = [
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED',
            403 => 'FORBIDDEN',
            404 => 'NOT_FOUND',
            405 => 'METHOD_NOT_ALLOWED',
            429 => 'RATE_LIMIT_EXCEEDED',
            500 => 'INTERNAL_SERVER_ERROR',
            503 => 'SERVICE_UNAVAILABLE'
        ];
        
        return $codes[$httpCode] ?? 'UNKNOWN_ERROR';
    }
    
    private function logRequest($type, $userId, $status) {
        // Log request for analytics/monitoring
        try {
            $logData = [
                'type' => $type,
                'user_id' => $userId,
                'status' => $status,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // Log to file (in production, use a proper logging system)
            $logMessage = json_encode($logData) . PHP_EOL;
            file_put_contents(APP_ROOT . '/logs/ai_requests.log', $logMessage, FILE_APPEND);
            
        } catch (Exception $e) {
            // Silently fail logging
            error_log("Request logging failed: " . $e->getMessage());
        }
    }
    
    // Batch processing for multiple analyses
    public function batchAnalyze() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendError('Method not allowed', 405);
            return;
        }
        
        $input = $this->getValidatedInput();
        $userId = $_SESSION['user_id'] ?? null;
        
        if (empty($input['analyses']) || !is_array($input['analyses'])) {
            $this->sendError('Analyses array is required', 400);
        }
        
        // Limit batch size
        if (count($input['analyses']) > 5) {
            $this->sendError('Maximum 5 analyses per batch allowed', 400);
        }
        
        $results = [];
        
        foreach ($input['analyses'] as $index => $analysis) {
            try {
                $type = $analysis['type'] ?? 'skill_gap';
                $result = $this->processRequest($type, $analysis, $userId);
                $results[] = [
                    'index' => $index,
                    'type' => $type,
                    'status' => 'success',
                    'result' => $result
                ];
            } catch (Exception $e) {
                $results[] = [
                    'index' => $index,
                    'type' => $analysis['type'] ?? 'unknown',
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        $response = [
            'status' => 'success',
            'total' => count($input['analyses']),
            'successful' => count(array_filter($results, fn($r) => $r['status'] === 'success')),
            'failed' => count(array_filter($results, fn($r) => $r['status'] === 'error')),
            'results' => $results
        ];
        
        echo json_encode($response);
    }
    
    // Get usage statistics (admin only)
    public function usageStats() {
        // Check if user is admin (implement your own authentication)
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->sendError('Unauthorized access', 403);
            return;
        }
        
        $period = $_GET['period'] ?? 'day'; // day, week, month
        
        // This would query your database for usage statistics
        // For now, return mock data
        $stats = [
            'period' => $period,
            'total_requests' => rand(100, 1000),
            'skill_gap_analysis' => rand(30, 300),
            'resume_reviews' => rand(20, 200),
            'interview_questions' => rand(10, 100),
            'task_feedback' => rand(5, 50),
            'unique_users' => rand(10, 100),
            'average_response_time' => rand(100, 500) / 100, // seconds
            'success_rate' => rand(85, 99) / 100 // percentage
        ];
        
        echo json_encode([
            'status' => 'success',
            'data' => $stats,
            'generated_at' => date('Y-m-d H:i:s')
        ]);
    }
}