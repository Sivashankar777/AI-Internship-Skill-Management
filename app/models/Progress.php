<?php

require_once APP_ROOT . '/app/core/Database.php';

class Progress {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get user progress
     */
    public function getUserProgress($userId) {
        try {
            $sql = "SELECT * FROM user_progress WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get user skills
     */
    public function getUserSkills($userId) {
        try {
            $sql = "SELECT s.name, us.proficiency_level FROM user_skills us 
                    JOIN skills s ON us.skill_id = s.id 
                    WHERE us.user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get login streak
     */
    public function getLoginStreak($userId) {
        return rand(1, 30); // Placeholder
    }
    
    /**
     * Get productivity score
     */
    public function getProductivityScore($userId) {
        return rand(70, 100); // Placeholder
    }
    
    /**
     * Get skill growth
     */
    public function getSkillGrowth($userId, $days = 30) {
        return rand(5, 25); // Placeholder percentage
    }
    
    /**
     * Get task efficiency
     */
    public function getTaskEfficiency($userId) {
        return rand(80, 100); // Placeholder
    }
    
    /**
     * Get consistency score
     */
    public function getConsistencyScore($userId) {
        return rand(70, 100); // Placeholder
    }
    
    /**
     * Get learning path progress
     */
    public function getLearningPathProgress($userId) {
        return [];
    }
    
    /**
     * Get user timeline
     */
    public function getUserTimeline($userId, $limit = 10) {
        return [];
    }
    
    /**
     * Get progress chart data
     */
    public function getProgressChartData($userId) {
        return [
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            'data' => [25, 45, 65, 80]
        ];
    }
    
    /**
     * Get skill chart data
     */
    public function getSkillChartData($userId) {
        return [
            'labels' => ['PHP', 'JavaScript', 'MySQL', 'React', 'Node.js'],
            'data' => [80, 70, 75, 60, 55]
        ];
    }
    
    /**
     * Get activity chart data
     */
    public function getActivityChartData($userId, $days = 7) {
        return [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'data' => [3, 5, 2, 4, 6, 1, 2]
        ];
    }
}
