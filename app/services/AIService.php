<?php

class AIService {
    private $apiKey;
    private $provider;
    private $db;
    private $rateLimitWindow = 3600; // 1 hour in seconds
    private $maxRequestsPerHour = 50;
    private $cacheTTL = 3600; // 1 hour cache

    public function __construct() {
        $this->apiKey = defined('AI_API_KEY') ? AI_API_KEY : '';
        $this->provider = defined('AI_PROVIDER') ? AI_PROVIDER : 'gemini';
        $this->db = Database::getInstance();
        $this->cache = new CacheService();
    }

    /**
     * Analyze skill gaps and provide personalized learning plan
     */
    public function analyzeSkillGap($userSkills, $requiredSkills, $userId = null) {
        // Validate inputs
        if (empty($userSkills) || empty($requiredSkills)) {
            return [
                'status' => 'error',
                'data' => 'User skills and required skills are required'
            ];
        }

        // Rate limiting check
        if (!$this->checkRateLimit($userId)) {
            return [
                'status' => 'error',
                'data' => 'Rate limit exceeded. Please try again later.'
            ];
        }

        // Check cache first
        $cacheKey = 'skill_gap_' . md5(serialize([$userSkills, $requiredSkills]));
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        // Prepare inputs
        $userSkillsList = is_array($userSkills) ? $userSkills : explode(',', $userSkills);
        $requiredSkillsList = is_array($requiredSkills) ? $requiredSkills : explode(',', $requiredSkills);
        
        // Clean and validate skills
        $userSkillsList = array_map('trim', array_filter($userSkillsList));
        $requiredSkillsList = array_map('trim', array_filter($requiredSkillsList));

        // Calculate similarity score (pre-processing)
        $matchScore = $this->calculateSkillMatchScore($userSkillsList, $requiredSkillsList);
        
        // Prepare prompt with context
        $prompt = $this->buildSkillGapPrompt($userSkillsList, $requiredSkillsList, $matchScore);
        
        // Call AI API
        $response = $this->callAPI($prompt, [
            'temperature' => 0.7,
            'max_tokens' => 1500
        ]);
        
        if ($response['status'] === 'success') {
            // Parse and structure the response
            $structuredResponse = $this->parseSkillGapResponse($response['data'], $matchScore);
            
            // Add metadata
            $structuredResponse['metadata'] = [
                'match_score' => $matchScore,
                'missing_skills' => $this->identifyMissingSkills($userSkillsList, $requiredSkillsList),
                'common_skills' => $this->identifyCommonSkills($userSkillsList, $requiredSkillsList),
                'generated_at' => date('Y-m-d H:i:s')
            ];
            
            // Cache the result
            $this->cache->set($cacheKey, $structuredResponse, $this->cacheTTL);
            
            // Log the request
            $this->logRequest('skill_gap_analysis', [
                'user_skills_count' => count($userSkillsList),
                'required_skills_count' => count($requiredSkillsList),
                'match_score' => $matchScore
            ], $structuredResponse, $userId);
            
            return $structuredResponse;
        }
        
        return $response;
    }

    /**
     * Review resume with ATS compatibility check
     */
    public function reviewResume($resumeText, $targetRole = null, $userId = null) {
        // Validate input
        if (empty($resumeText) || strlen(trim($resumeText)) < 50) {
            return [
                'status' => 'error',
                'data' => 'Resume text is too short. Minimum 50 characters required.'
            ];
        }

        // Rate limiting check
        if (!$this->checkRateLimit($userId)) {
            return [
                'status' => 'error',
                'data' => 'Rate limit exceeded. Please try again later.'
            ];
        }

        // Check cache
        $cacheKey = 'resume_review_' . md5($resumeText . $targetRole);
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        // Pre-process resume text
        $cleanResume = $this->cleanResumeText($resumeText);
        
        // Analyze resume metrics
        $resumeMetrics = $this->analyzeResumeMetrics($cleanResume);
        
        // Build context-aware prompt
        $prompt = $this->buildResumeReviewPrompt($cleanResume, $targetRole, $resumeMetrics);
        
        // Call AI API
        $response = $this->callAPI($prompt, [
            'temperature' => 0.5,
            'max_tokens' => 2000
        ]);
        
        if ($response['status'] === 'success') {
            // Parse response into structured format
            $structuredResponse = $this->parseResumeReviewResponse($response['data'], $resumeMetrics);
            
            // Add metrics and suggestions
            $structuredResponse['metrics'] = $resumeMetrics;
            $structuredResponse['suggestions'] = $this->generateResumeSuggestions($resumeMetrics);
            $structuredResponse['generated_at'] = date('Y-m-d H:i:s');
            
            // Cache result
            $this->cache->set($cacheKey, $structuredResponse, $this->cacheTTL);
            
            // Log request
            $this->logRequest('resume_review', [
                'resume_length' => strlen($cleanResume),
                'target_role' => $targetRole,
                'word_count' => $resumeMetrics['word_count']
            ], $structuredResponse, $userId);
            
            return $structuredResponse;
        }
        
        return $response;
    }

    /**
     * Generate interview questions based on skills
     */
    public function generateInterviewQuestions($skills, $difficulty = 'intermediate', $userId = null) {
        $skillsList = is_array($skills) ? $skills : explode(',', $skills);
        $skillsList = array_map('trim', array_filter($skillsList));
        
        if (empty($skillsList)) {
            return ['status' => 'error', 'data' => 'No skills provided'];
        }

        $cacheKey = 'interview_questions_' . md5(serialize([$skillsList, $difficulty]));
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        $prompt = $this->buildInterviewQuestionsPrompt($skillsList, $difficulty);
        
        $response = $this->callAPI($prompt, [
            'temperature' => 0.8,
            'max_tokens' => 1200
        ]);
        
        if ($response['status'] === 'success') {
            $parsedResponse = $this->parseInterviewQuestionsResponse($response['data']);
            
            $this->cache->set($cacheKey, $parsedResponse, $this->cacheTTL);
            $this->logRequest('interview_questions', ['skills' => $skillsList], $parsedResponse, $userId);
            
            return $parsedResponse;
        }
        
        return $response;
    }

    /**
     * Analyze task submission and provide feedback
     */
    public function analyzeTaskSubmission($taskDescription, $submissionText, $userId = null) {
        if (empty($taskDescription) || empty($submissionText)) {
            return ['status' => 'error', 'data' => 'Task description and submission are required'];
        }

        $cacheKey = 'task_feedback_' . md5($taskDescription . $submissionText);
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        $prompt = $this->buildTaskFeedbackPrompt($taskDescription, $submissionText);
        
        $response = $this->callAPI($prompt, [
            'temperature' => 0.6,
            'max_tokens' => 1800
        ]);
        
        if ($response['status'] === 'success') {
            $parsedResponse = $this->parseTaskFeedbackResponse($response['data']);
            
            $this->cache->set($cacheKey, $parsedResponse, 1800); // 30 minutes cache
            $this->logRequest('task_feedback', ['task_length' => strlen($taskDescription)], $parsedResponse, $userId);
            
            return $parsedResponse;
        }
        
        return $response;
    }

    /**
     * Call AI API with enhanced error handling and retries
     */
    private function callAPI($prompt, $options = []) {
        // Configuration check
        if ($this->apiKey === 'YOUR_API_KEY_HERE' || empty($this->apiKey)) {
            return [
                'status' => 'error',
                'data' => 'AI service is not configured. Please contact administrator.',
                'error_code' => 'CONFIG_ERROR'
            ];
        }

        // Add system context to prompt
        $systemPrompt = "You are an expert career mentor and technical advisor with 15+ years of experience in software development, hiring, and mentorship. Provide practical, actionable advice in a professional but approachable tone. Always respond in valid HTML format with proper semantic markup.";
        $fullPrompt = $systemPrompt . "\n\n" . $prompt;

        $retryCount = 0;
        $maxRetries = 2;
        
        while ($retryCount <= $maxRetries) {
            try {
                if ($this->provider === 'openai') {
                    $result = $this->callOpenAI($fullPrompt, $options);
                } else {
                    $result = $this->callGemini($fullPrompt, $options);
                }

                if ($result['status'] === 'success') {
                    return $result;
                }
                
                // If error is rate limit, wait and retry
                if (strpos($result['data'] ?? '', 'rate limit') !== false && $retryCount < $maxRetries) {
                    sleep(pow(2, $retryCount)); // Exponential backoff
                    $retryCount++;
                    continue;
                }
                
                return $result;
                
            } catch (Exception $e) {
                error_log("AI API Exception: " . $e->getMessage());
                
                if ($retryCount < $maxRetries) {
                    sleep(pow(2, $retryCount));
                    $retryCount++;
                    continue;
                }
                
                return [
                    'status' => 'error',
                    'data' => 'AI service temporarily unavailable. Please try again later.',
                    'error_code' => 'SERVICE_UNAVAILABLE'
                ];
            }
        }
    }

    private function callGemini($prompt, $options = []) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'maxOutputTokens' => $options['max_tokens'] ?? 1500,
                'topP' => 0.95,
                'topK' => 40
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];

        $response = $this->sendRequest($url, $data);
        
        if (isset($response['error'])) {
            return [
                'status' => 'error',
                'data' => 'Gemini API Error: ' . ($response['error']['message'] ?? 'Unknown error'),
                'error_code' => 'GEMINI_ERROR'
            ];
        }

        if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $response['candidates'][0]['content']['parts'][0]['text'];
            
            // Estimate tokens (rough approximation)
            $estimatedTokens = intval(strlen($text) / 4);
            
            return [
                'status' => 'success',
                'data' => $this->formatResponse($text),
                'tokens_used' => $estimatedTokens
            ];
        }

        return [
            'status' => 'error',
            'data' => 'AI Provider returned invalid response format.',
            'error_code' => 'INVALID_RESPONSE'
        ];
    }

    private function callOpenAI($prompt, $options = []) {
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $data = [
            'model' => $options['model'] ?? 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'You are an expert career mentor and technical advisor. Provide practical, actionable advice.'
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 1500,
            'top_p' => 0.95
        ];

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $response = $this->sendRequest($url, $data, $headers);
        
        if (isset($response['error'])) {
            return [
                'status' => 'error',
                'data' => 'OpenAI API Error: ' . ($response['error']['message'] ?? 'Unknown error'),
                'error_code' => 'OPENAI_ERROR'
            ];
        }

        if (isset($response['usage'])) {
            $tokensUsed = $response['usage']['total_tokens'];
        }

        if (isset($response['choices'][0]['message']['content'])) {
            return [
                'status' => 'success',
                'data' => $this->formatResponse($response['choices'][0]['message']['content']),
                'tokens_used' => $tokensUsed ?? 0
            ];
        }

        return [
            'status' => 'error',
            'data' => 'AI Provider returned invalid response format.',
            'error_code' => 'INVALID_RESPONSE'
        ];
    }

    private function sendRequest($url, $data, $customHeaders = []) {
        $ch = curl_init($url);
        
        $headers = array_merge([
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: AI-Internship-Manager/1.0'
        ], $customHeaders);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FAILONERROR => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => $error];
        }

        $decoded = json_decode($response, true);
        
        if ($httpCode !== 200) {
            return [
                'error' => [
                    'message' => 'HTTP ' . $httpCode,
                    'code' => $httpCode,
                    'details' => $decoded['error'] ?? $response
                ]
            ];
        }

        return $decoded ?: $response;
    }

    private function formatResponse($text) {
        // Convert markdown to HTML
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/`(.+?)`/', '<code>$1</code>', $html);
        
        // Convert numbered lists
        $html = preg_replace('/^\d+\.\s+(.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ol>$1</ol>', $html);
        
        // Convert bullet points
        $html = preg_replace('/^[-*]\s+(.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $html);
        
        // Convert line breaks and paragraphs
        $html = preg_replace('/\n\s*\n/', '</p><p>', $html);
        $html = '<p>' . preg_replace('/\n/', '<br>', $html) . '</p>';
        
        // Clean up nested lists
        $html = str_replace(['<ul><li>', '</li></ul>'], ['<ul>', '</ul>'], $html);
        $html = str_replace(['<ol><li>', '</li></ol>'], ['<ol>', '</ol>'], $html);
        
        return $html;
    }

    private function calculateSkillMatchScore($userSkills, $requiredSkills) {
        $userSkillsLower = array_map('strtolower', $userSkills);
        $requiredSkillsLower = array_map('strtolower', $requiredSkills);
        
        $common = array_intersect($userSkillsLower, $requiredSkillsLower);
        $totalRequired = count($requiredSkillsLower);
        
        if ($totalRequired === 0) return 0;
        
        return round((count($common) / $totalRequired) * 100, 1);
    }

    private function identifyMissingSkills($userSkills, $requiredSkills) {
        $userSkillsLower = array_map('strtolower', $userSkills);
        $requiredSkillsLower = array_map('strtolower', $requiredSkills);
        
        return array_diff($requiredSkillsLower, $userSkillsLower);
    }

    private function identifyCommonSkills($userSkills, $requiredSkills) {
        $userSkillsLower = array_map('strtolower', $userSkills);
        $requiredSkillsLower = array_map('strtolower', $requiredSkills);
        
        return array_intersect($userSkillsLower, $requiredSkillsLower);
    }

    private function buildSkillGapPrompt($userSkills, $requiredSkills, $matchScore) {
        return "As a senior tech mentor, analyze the skill gap between these two sets:

Current Skills: " . implode(', ', $userSkills) . "

Required Skills for Target Role: " . implode(', ', $requiredSkills) . "

Match Score: " . $matchScore . "%

Please provide:
1. Gap Analysis: Missing skills categorized by priority (High/Medium/Low)
2. Learning Plan: 30-day actionable plan with weekly milestones
3. Resource Recommendations: Free and paid resources for each skill
4. Project Suggestions: Mini-projects to practice missing skills
5. Confidence Timeline: Estimated time to become proficient

Format the response in HTML with clear sections and actionable items.";
    }

    private function parseSkillGapResponse($htmlResponse, $matchScore) {
        return [
            'status' => 'success',
            'analysis' => $htmlResponse,
            'summary' => [
                'match_score' => $matchScore,
                'recommendation' => $matchScore >= 70 ? 'You are well-prepared for this role' :
                                  ($matchScore >= 40 ? 'You need to develop some additional skills' :
                                  'Significant skill development required')
            ],
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }

    private function cleanResumeText($text) {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        // Remove special characters but keep basic punctuation
        $text = preg_replace('/[^\w\s.,;:!?@\-()\n]/', '', $text);
        // Limit length
        return substr($text, 0, 10000);
    }

    private function analyzeResumeMetrics($resumeText) {
        $words = str_word_count($resumeText);
        $chars = strlen($resumeText);
        $sentences = preg_match_all('/[.!?]+/', $resumeText);
        
        // Simple keyword detection (can be enhanced)
        $keywords = ['experience', 'skills', 'education', 'projects', 'achievements'];
        $foundKeywords = [];
        
        foreach ($keywords as $keyword) {
            if (stripos($resumeText, $keyword) !== false) {
                $foundKeywords[] = $keyword;
            }
        }
        
        return [
            'word_count' => $words,
            'character_count' => $chars,
            'sentence_count' => $sentences,
            'keywords_found' => $foundKeywords,
            'readability_score' => $words > 0 ? round($words / max(1, $sentences)) : 0
        ];
    }

    private function buildResumeReviewPrompt($resumeText, $targetRole, $metrics) {
        $roleContext = $targetRole ? "Target Role: $targetRole\n\n" : "";
        
        return "As an expert ATS Resume Reviewer with 10+ years in tech recruitment, review this resume:

" . $roleContext . 
"Resume Metrics:
- Word Count: " . $metrics['word_count'] . "
- Keywords Found: " . implode(', ', $metrics['keywords_found']) . "

Resume Content:
" . $resumeText . "

Provide analysis in these sections:
1. ATS Compatibility Score (1-100) and why
2. Strengths: What works well in this resume
3. Weaknesses: Areas needing improvement
4. Critical Fixes: Must-change items for better results
5. Keyword Optimization: Missing important keywords
6. Formatting Suggestions: For better readability
7. Action Plan: Specific steps to improve

Format as HTML with clear headings and bullet points.";
    }

    private function parseResumeReviewResponse($htmlResponse, $metrics) {
        return [
            'status' => 'success',
            'review' => $htmlResponse,
            'metrics' => $metrics,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }

    private function generateResumeSuggestions($metrics) {
        $suggestions = [];
        
        if ($metrics['word_count'] < 300) {
            $suggestions[] = 'Consider adding more detail to your experience section';
        }
        
        if ($metrics['word_count'] > 800) {
            $suggestions[] = 'Your resume might be too long. Try to keep it concise';
        }
        
        if (count($metrics['keywords_found']) < 3) {
            $suggestions[] = 'Add more relevant keywords from the job description';
        }
        
        return $suggestions;
    }

    private function buildInterviewQuestionsPrompt($skills, $difficulty) {
        return "Generate 10 technical interview questions for these skills: " . implode(', ', $skills) . "

Difficulty Level: " . $difficulty . "

For each question, provide:
1. The question text
2. Expected answer points
3. Difficulty rating (Easy/Medium/Hard)
4. Tips for answering

Format as HTML with clear question blocks.";
    }

    private function parseInterviewQuestionsResponse($htmlResponse) {
        return [
            'status' => 'success',
            'questions' => $htmlResponse,
            'count' => substr_count($htmlResponse, '<h3>') + substr_count($htmlResponse, '<strong>Question'),
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }

    private function buildTaskFeedbackPrompt($taskDescription, $submissionText) {
        return "As a senior developer mentor, provide feedback on this task submission:

Task Description:
" . $taskDescription . "

Submission:
" . $submissionText . "

Provide feedback in these areas:
1. Completeness: Did they address all requirements?
2. Code Quality: Best practices, readability, efficiency
3. Improvements: Specific suggestions for better implementation
4. Learning Points: Key concepts to reinforce
5. Grade: A-F with justification

Format as HTML with clear sections.";
    }

    private function parseTaskFeedbackResponse($htmlResponse) {
        return [
            'status' => 'success',
            'feedback' => $htmlResponse,
            'generated_at' => date('Y-m-d H:i:s')
        ];
    }

    private function checkRateLimit($userId) {
        if (!$userId) return true; // No rate limit for anonymous users (adjust as needed)
        
        $sql = "SELECT COUNT(*) as count FROM ai_logs 
                WHERE user_id = :user_id 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] < $this->maxRequestsPerHour;
    }

    private function logRequest($type, $inputSummary, $output, $userId = null) {
        try {
            $outputData = is_array($output) ? json_encode($output) : $output;
            $tokensUsed = isset($output['tokens_used']) ? $output['tokens_used'] : 0;
            
            $sql = "INSERT INTO ai_logs 
                    (user_id, request_type, input_summary, ai_response, tokens_used, response_time, ip_address, user_agent) 
                    VALUES (:uid, :type, :input, :output, :tokens, :rtime, :ip, :agent)";
            
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([
                ':uid' => $userId,
                ':type' => $type,
                ':input' => json_encode($inputSummary),
                ':output' => substr($outputData, 0, 65000),
                ':tokens' => $tokensUsed,
                ':rtime' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                ':agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("AI Logging Failed: " . $e->getMessage());
            return false;
        }
    }
}

// Optional Cache Service (simple implementation)
class CacheService {
    private $cacheDir;
    
    public function __construct() {
        $this->cacheDir = __DIR__ . '/../cache/ai/';
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function get($key) {
        $file = $this->cacheDir . md5($key) . '.cache';
        
        if (file_exists($file) && (time() - filemtime($file)) < 3600) {
            return unserialize(file_get_contents($file));
        }
        
        return false;
    }
    
    public function set($key, $data, $ttl = 3600) {
        $file = $this->cacheDir . md5($key) . '.cache';
        return file_put_contents($file, serialize($data));
    }
    
    public function delete($key) {
        $file = $this->cacheDir . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    }
}