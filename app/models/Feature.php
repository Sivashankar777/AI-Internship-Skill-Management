<?php

require_once APP_ROOT . '/app/core/Database.php';

class Feature {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all features
     */
    public function getAllFeatures() {
        try {
            $sql = "SELECT * FROM features WHERE is_active = 1 ORDER BY display_order ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Return default features if table doesn't exist
            return [
                [
                    'id' => 1,
                    'title' => 'AI-Powered Skill Analysis',
                    'description' => 'Get personalized insights on your skills and areas for improvement',
                    'icon' => 'bi-robot',
                    'color' => 'primary'
                ],
                [
                    'id' => 2,
                    'title' => 'Smart Mentor Matching',
                    'description' => 'Connect with industry mentors based on your goals and interests',
                    'icon' => 'bi-people',
                    'color' => 'success'
                ],
                [
                    'id' => 3,
                    'title' => 'Interactive Learning Paths',
                    'description' => 'Follow customized learning paths tailored to your career goals',
                    'icon' => 'bi-map',
                    'color' => 'info'
                ],
                [
                    'id' => 4,
                    'title' => 'Real-Time Progress Tracking',
                    'description' => 'Monitor your growth with detailed analytics and insights',
                    'icon' => 'bi-graph-up-arrow',
                    'color' => 'warning'
                ],
                [
                    'id' => 5,
                    'title' => 'Task Management',
                    'description' => 'Manage and track internship tasks with AI-enhanced feedback',
                    'icon' => 'bi-list-task',
                    'color' => 'danger'
                ],
                [
                    'id' => 6,
                    'title' => 'Resume Builder',
                    'description' => 'Create ATS-friendly resumes with AI suggestions',
                    'icon' => 'bi-file-earmark-text',
                    'color' => 'secondary'
                ]
            ];
        }
    }
}
