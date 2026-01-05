<?php

require_once APP_ROOT . '/app/services/AIService.php';

class AIController {
    public function analyze() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? 'skill_gap';
        
        $ai = new AIService();
        $response = [];

        if ($type === 'skill_gap') {
            $userSkills = $input['user_skills'] ?? ['HTML', 'CSS'];
            $requiredSkills = $input['required_skills'] ?? ['PHP', 'MySQL', 'React'];
            $response = $ai->analyzeSkillGap($userSkills, $requiredSkills);
        } elseif ($type === 'resume_review') {
            $resumeText = $input['resume_text'] ?? '';
            $response = $ai->reviewResume($resumeText);
        }

        echo json_encode($response);
    }
}
