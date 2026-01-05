<?php

require_once APP_ROOT . '/app/core/Database.php';

class NotificationService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get user notifications
     */
    public function getUserNotifications($userId, $limit = 10) {
        try {
            $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get paginated notifications
     */
    public function getPaginatedNotifications($userId, $page = 1, $perPage = 20) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching paginated notifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total notifications count
     */
    public function getTotalNotifications($userId) {
        try {
            $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting notifications: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get unread count
     */
    public function getUnreadCount($userId) {
        try {
            $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting unread notifications: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId) {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $notificationId, ':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all as read
     */
    public function markAllAsRead($userId) {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Error marking all notifications as read: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create notification
     */
    public function create($userId, $type, $title, $message, $link = null) {
        try {
            $sql = "INSERT INTO notifications (user_id, type, title, message, link, created_at) 
                    VALUES (:user_id, :type, :title, :message, :link, NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':type' => $type,
                ':title' => $title,
                ':message' => $message,
                ':link' => $link
            ]);
        } catch (PDOException $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send task assigned notification
     */
    public function sendTaskAssignedNotification($userId, $taskId, $taskTitle) {
        return $this->create(
            $userId,
            'task_assigned',
            'New Task Assigned',
            "You have been assigned a new task: {$taskTitle}",
            "/tasks/{$taskId}"
        );
    }
    
    /**
     * Send task created notification
     */
    public function sendTaskCreatedNotification($taskId, $taskTitle) {
        // Notify admins
        error_log("Task created notification: {$taskTitle}");
        return true;
    }
    
    /**
     * Send task updated notification
     */
    public function sendTaskUpdatedNotification($taskId, $taskTitle) {
        error_log("Task updated notification: {$taskTitle}");
        return true;
    }
    
    /**
     * Send submission notification
     */
    public function sendSubmissionNotification($mentorId, $taskId, $internId) {
        return $this->create(
            $mentorId,
            'submission',
            'New Task Submission',
            "A task submission is awaiting your review",
            "/tasks/{$taskId}"
        );
    }
    
    /**
     * Send review notification
     */
    public function sendReviewNotification($userId, $taskId, $reviewData) {
        $status = $reviewData['status'] ?? 'reviewed';
        return $this->create(
            $userId,
            'review',
            'Task Review Received',
            "Your task has been {$status} with a score of {$reviewData['score']}/100",
            "/tasks/{$taskId}"
        );
    }
}
