<?php

require_once APP_ROOT . '/app/core/Database.php';

class StatsService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get platform statistics
     */
    public function getPlatformStatistics() {
        return [
            'total_users' => $this->getTotalUsers(),
            'total_interns' => $this->getTotalInterns(),
            'total_mentors' => $this->getTotalMentors(),
            'total_tasks' => $this->getTotalTasks(),
            'completed_tasks' => $this->getCompletedTasks(),
            'active_internships' => $this->getActiveInternships(),
            'success_rate' => $this->getSuccessRate()
        ];
    }
    
    /**
     * Get total users
     */
    private function getTotalUsers() {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE status = 'active'";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get total interns
     */
    private function getTotalInterns() {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE role = 'intern' AND status = 'active'";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get total mentors
     */
    private function getTotalMentors() {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'active'";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get total tasks
     */
    private function getTotalTasks() {
        try {
            $sql = "SELECT COUNT(*) FROM tasks";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get completed tasks
     */
    private function getCompletedTasks() {
        try {
            $sql = "SELECT COUNT(*) FROM tasks WHERE status = 'completed'";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get active internships
     */
    private function getActiveInternships() {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE role = 'intern' AND status = 'active'";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Calculate success rate
     */
    private function getSuccessRate() {
        $total = $this->getTotalTasks();
        $completed = $this->getCompletedTasks();
        
        if ($total === 0) return 95; // Default value
        
        return round(($completed / $total) * 100);
    }
    
    /**
     * Track referral view
     */
    public function trackReferralView($referralCode) {
        try {
            $sql = "UPDATE referrals SET views = views + 1 WHERE code = :code";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':code' => $referralCode]);
        } catch (PDOException $e) {
            error_log("Error tracking referral: " . $e->getMessage());
        }
    }
}
