<?php

require_once APP_ROOT . '/app/models/User.php'; // Ideally we'd have a Task model

class TaskController {
    
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create() {
        if ($_SESSION['role'] !== 'mentor' && $_SESSION['role'] !== 'admin') {
            die('Unauthorized');
        }
        require_once APP_ROOT . '/app/views/tasks/create.php';
    }

    public function store() {
        if ($_SESSION['role'] !== 'mentor' && $_SESSION['role'] !== 'admin') {
            die('Unauthorized');
        }

        $title = $_POST['title'];
        $description = $_POST['description'];
        $skills = json_encode(explode(',', $_POST['required_skills'])); // Simple array conversion
        $deadline = $_POST['deadline'];
        
        $sql = "INSERT INTO tasks (title, description, required_skills, deadline, created_by, status) 
                VALUES (:title, :description, :required_skills, :deadline, :created_by, 'pending')";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':required_skills' => $skills,
            ':deadline' => $deadline,
            ':created_by' => $_SESSION['user_id']
        ]);

        header("Location: " . BASE_URL . "/dashboard");
    }
}
