<?php

require_once APP_ROOT . '/app/core/Database.php';

class Skill {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all skills
     */
    public function getAllSkills() {
        try {
            $sql = "SELECT * FROM skills ORDER BY name ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Return default skills if table doesn't exist
            return [
                ['id' => 1, 'name' => 'PHP'],
                ['id' => 2, 'name' => 'JavaScript'],
                ['id' => 3, 'name' => 'MySQL'],
                ['id' => 4, 'name' => 'HTML/CSS'],
                ['id' => 5, 'name' => 'React'],
                ['id' => 6, 'name' => 'Node.js'],
                ['id' => 7, 'name' => 'Python'],
                ['id' => 8, 'name' => 'Git']
            ];
        }
    }
    
    /**
     * Find or create skill
     */
    public function findOrCreate($skillName) {
        try {
            // Try to find existing skill
            $sql = "SELECT id FROM skills WHERE name = :name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':name' => $skillName]);
            $skill = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($skill) {
                return $skill['id'];
            }
            
            // Create new skill
            $sql = "INSERT INTO skills (name, created_at) VALUES (:name, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':name' => $skillName]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Return skill name as fallback
            return $skillName;
        }
    }
    
    /**
     * Find skill by ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM skills WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Find skill by name
     */
    public function findByName($name) {
        try {
            $sql = "SELECT * FROM skills WHERE name = :name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':name' => $name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
}
