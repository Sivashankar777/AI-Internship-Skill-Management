<?php

require_once APP_ROOT . '/app/core/Database.php';

class Testimonial {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get featured testimonials
     */
    public function getFeaturedTestimonials($limit = 6) {
        try {
            $sql = "SELECT * FROM testimonials WHERE is_featured = 1 ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Return default testimonials if table doesn't exist
            return [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'role' => 'Software Intern',
                    'company' => 'Tech Corp',
                    'content' => 'This platform helped me land my dream internship. The AI-powered skill matching is incredible!',
                    'rating' => 5,
                    'avatar' => 'JD'
                ],
                [
                    'id' => 2,
                    'name' => 'Sarah Smith',
                    'role' => 'UX Design Intern',
                    'company' => 'Design Studio',
                    'content' => 'The mentorship matching feature connected me with amazing industry professionals.',
                    'rating' => 5,
                    'avatar' => 'SS'
                ],
                [
                    'id' => 3,
                    'name' => 'Mike Johnson',
                    'role' => 'Data Science Intern',
                    'company' => 'Data Inc',
                    'content' => 'I improved my skills significantly through the personalized learning paths.',
                    'rating' => 4,
                    'avatar' => 'MJ'
                ]
            ];
        }
    }
    
    /**
     * Get all testimonials
     */
    public function getAll() {
        try {
            $sql = "SELECT * FROM testimonials ORDER BY created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
