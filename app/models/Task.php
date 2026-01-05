<?php

require_once APP_ROOT . '/app/core/Database.php';

class Task {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new task
     */
    public function create($data) {
        try {
            $skills = is_array($data['required_skills']) ? json_encode($data['required_skills']) : $data['required_skills'];
            
            $sql = "INSERT INTO tasks (title, description, required_skills, deadline, estimated_time, difficulty, category, resources, success_criteria, created_by, assigned_to, status, created_at) 
                    VALUES (:title, :description, :required_skills, :deadline, :estimated_time, :difficulty, :category, :resources, :success_criteria, :created_by, :assigned_to, :status, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':required_skills' => $skills,
                ':deadline' => $data['deadline'],
                ':estimated_time' => $data['estimated_time'],
                ':difficulty' => $data['difficulty'],
                ':category' => $data['category'],
                ':resources' => $data['resources'],
                ':success_criteria' => $data['success_criteria'],
                ':created_by' => $data['created_by'],
                ':assigned_to' => $data['assigned_to'],
                ':status' => $data['status'] ?? 'pending'
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Task creation error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Find task by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM tasks WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding task: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update task
     */
    public function update($id, $data) {
        try {
            $skills = is_array($data['required_skills']) ? json_encode($data['required_skills']) : $data['required_skills'];
            
            $sql = "UPDATE tasks SET title = :title, description = :description, required_skills = :required_skills, 
                    deadline = :deadline, estimated_time = :estimated_time, difficulty = :difficulty, 
                    category = :category, resources = :resources, success_criteria = :success_criteria, 
                    status = :status, updated_at = NOW() WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':required_skills' => $skills,
                ':deadline' => $data['deadline'],
                ':estimated_time' => $data['estimated_time'],
                ':difficulty' => $data['difficulty'],
                ':category' => $data['category'],
                ':resources' => $data['resources'],
                ':success_criteria' => $data['success_criteria'],
                ':status' => $data['status']
            ]);
        } catch (PDOException $e) {
            error_log("Task update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete task
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM tasks WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Task deletion error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user tasks
     */
    public function getUserTasks($userId, $limit = 10) {
        try {
            $sql = "SELECT * FROM tasks WHERE assigned_to = :user_id ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get completed tasks
     */
    public function getCompletedTasks($userId) {
        try {
            $sql = "SELECT * FROM tasks WHERE assigned_to = :user_id AND status = 'completed'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get pending tasks
     */
    public function getPendingTasks($userId) {
        try {
            $sql = "SELECT * FROM tasks WHERE assigned_to = :user_id AND status IN ('pending', 'assigned')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get active tasks
     */
    public function getActiveTasks($userId) {
        try {
            $sql = "SELECT * FROM tasks WHERE assigned_to = :user_id AND status = 'in_progress'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get upcoming tasks
     */
    public function getUpcomingTasks($userId, $limit = 5) {
        try {
            $sql = "SELECT * FROM tasks WHERE assigned_to = :user_id AND deadline >= CURDATE() ORDER BY deadline ASC LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get mentor tasks
     */
    public function getMentorTasks($mentorId, $page = 1, $perPage = 10, $filters = []) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM tasks WHERE created_by = :mentor_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':mentor_id', $mentorId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get pending reviews
     */
    public function getPendingReviews($mentorId) {
        try {
            $sql = "SELECT t.*, s.id as submission_id FROM tasks t 
                    JOIN task_submissions s ON t.id = s.task_id 
                    WHERE t.created_by = :mentor_id AND s.status = 'submitted'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':mentor_id' => $mentorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get recent feedback given
     */
    public function getRecentFeedbackGiven($mentorId, $limit = 5) {
        return [];
    }
    
    /**
     * Get recent submissions
     */
    public function getRecentSubmissions($userId, $limit = 5) {
        return [];
    }
    
    /**
     * Get all tasks (admin)
     */
    public function getAllTasks($page = 1, $perPage = 10, $filters = []) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM tasks ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get total tasks count
     */
    public function getTotalTasksCount($filters = []) {
        try {
            $sql = "SELECT COUNT(*) FROM tasks";
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get total tasks for user
     */
    public function getTotalTasks($userId) {
        try {
            $sql = "SELECT COUNT(*) FROM tasks WHERE assigned_to = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get completed task count
     */
    public function getCompletedTaskCount($userId) {
        try {
            $sql = "SELECT COUNT(*) FROM tasks WHERE assigned_to = :user_id AND status = 'completed'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get average score
     */
    public function getAverageScore($userId) {
        return 0;
    }
    
    /**
     * Get task categories
     */
    public function getTaskCategories() {
        return ['development', 'design', 'research', 'documentation', 'testing', 'other'];
    }
    
    /**
     * Get difficulty levels
     */
    public function getDifficultyLevels() {
        return ['beginner', 'intermediate', 'advanced', 'expert'];
    }
    
    /**
     * Get task statuses
     */
    public function getTaskStatuses() {
        return ['pending', 'assigned', 'in_progress', 'submitted', 'reviewed', 'completed', 'cancelled'];
    }
    
    /**
     * Update task status
     */
    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE tasks SET status = :status, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id, ':status' => $status]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Mark task as viewed
     */
    public function markAsViewed($taskId, $userId) {
        return true;
    }
    
    /**
     * Get task submissions
     */
    public function getTaskSubmissions($taskId) {
        try {
            $sql = "SELECT * FROM task_submissions WHERE task_id = :task_id ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':task_id' => $taskId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get task comments
     */
    public function getTaskComments($taskId) {
        return [];
    }
    
    /**
     * Get related tasks
     */
    public function getRelatedTasks($taskId) {
        return [];
    }
    
    /**
     * Create submission
     */
    public function createSubmission($data) {
        try {
            $sql = "INSERT INTO task_submissions (task_id, user_id, submission_text, attachment_path, status, created_at) 
                    VALUES (:task_id, :user_id, :submission_text, :attachment_path, :status, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':task_id' => $data['task_id'],
                ':user_id' => $data['user_id'],
                ':submission_text' => $data['submission_text'],
                ':attachment_path' => $data['attachment_path'],
                ':status' => $data['status']
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    /**
     * Get submission by ID
     */
    public function getSubmissionById($id) {
        try {
            $sql = "SELECT * FROM task_submissions WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Update submission
     */
    public function updateSubmission($id, $data) {
        try {
            $sql = "UPDATE task_submissions SET score = :score, feedback = :feedback, status = :status, reviewed_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':score' => $data['score'],
                ':feedback' => $data['feedback'],
                ':status' => $data['status']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Store AI feedback
     */
    public function storeAIFeedback($taskId, $feedback) {
        return true;
    }
    
    /**
     * Get upcoming deadlines
     */
    public function getUpcomingDeadlines($userId, $days = 7) {
        try {
            $sql = "SELECT * FROM tasks WHERE assigned_to = :user_id AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY) ORDER BY deadline ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Additional stub methods
    public function getMentorTasksCount($userId, $filters = []) { return 0; }
    public function getUserTasksCount($userId, $filters = []) { return 0; }
    public function getMentorTaskCount($userId) { return 0; }
    public function getPendingReviewCount($userId) { return 0; }
    public function getMentorUpcomingDeadlines($userId, $days) { return []; }
    public function getSystemUpcomingDeadlines($days) { return []; }
    public function getTotalSystemTasks() { return 0; }
    public function assignToUser($taskId, $userId) { return true; }
}
