<?php

require_once APP_ROOT . '/app/services/SessionService.php';
require_once APP_ROOT . '/app/services/AIService.php';
require_once APP_ROOT . '/app/services/NotificationService.php';
require_once APP_ROOT . '/app/services/ValidationService.php';
require_once APP_ROOT . '/app/models/Task.php';
require_once APP_ROOT . '/app/models/User.php';
require_once APP_ROOT . '/app/models/Skill.php';

class TaskController {
    
    private $sessionService;
    private $aiService;
    private $notificationService;
    private $validationService;
    private $taskModel;
    private $userModel;
    private $skillModel;

    public function __construct() {
        $this->sessionService = new SessionService();
        $this->aiService = new AIService();
        $this->notificationService = new NotificationService();
        $this->validationService = new ValidationService();
        $this->taskModel = new Task();
        $this->userModel = new User();
        $this->skillModel = new Skill();
        
        $this->sessionService->start();
    }

    // SHOW CREATE TASK FORM
    public function create() {
        // 🔐 Authorization check
        if (!$this->canCreateTasks()) {
            $this->sessionService->setFlash('error', 'You are not authorized to create tasks');
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        // Get available skills for autocomplete
        $skills = $this->skillModel->getAllSkills();
        
        // Get available interns for assignment
        $availableInterns = $this->userModel->getAvailableInterns();
        
        // Get categories
        $categories = $this->taskModel->getTaskCategories();
        
        // Get difficulty levels
        $difficultyLevels = $this->taskModel->getDifficultyLevels();
        
        // Get AI suggestions for task templates
        $aiSuggestions = $this->getAITaskSuggestions();

        $data = [
            'page_title' => 'Create New Task',
            'skills' => $skills,
            'available_interns' => $availableInterns,
            'categories' => $categories,
            'difficulty_levels' => $difficultyLevels,
            'ai_suggestions' => $aiSuggestions,
            'form_data' => $this->sessionService->getFlash('form_data') ?? [],
            'errors' => $this->sessionService->getFlash('errors') ?? [],
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
                ['label' => 'Tasks', 'url' => BASE_URL . '/tasks'],
                ['label' => 'Create Task', 'url' => BASE_URL . '/tasks/create', 'active' => true]
            ]
        ];

        require APP_ROOT . '/app/views/tasks/create.php';
    }

    // STORE NEW TASK
    public function store() {
        // 🔐 Authorization check
        if (!$this->canCreateTasks()) {
            $this->sessionService->setFlash('error', 'You are not authorized to create tasks');
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken()) {
            $this->sessionService->setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectWithFormData();
            exit;
        }

        // Sanitize and validate input
        $data = $this->sanitizeTaskData($_POST);
        $validationErrors = $this->validateTaskData($data);

        if (!empty($validationErrors)) {
            $this->sessionService->setFlash('errors', $validationErrors);
            $this->sessionService->setFlash('form_data', $data);
            $this->redirectWithFormData();
            exit;
        }

        // Process skills
        $skills = $this->processSkills($data['required_skills']);
        
        // Generate AI-enhanced description if enabled
        if (isset($data['enhance_with_ai']) && $data['enhance_with_ai']) {
            $data['description'] = $this->enhanceDescriptionWithAI($data);
        }

        try {
            // Create task
            $taskId = $this->taskModel->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'required_skills' => $skills,
                'deadline' => $data['deadline'],
                'estimated_time' => $data['estimated_time'] ?? null,
                'difficulty' => $data['difficulty'] ?? 'intermediate',
                'category' => $data['category'] ?? null,
                'resources' => $data['resources'] ?? null,
                'success_criteria' => $data['success_criteria'] ?? null,
                'created_by' => $this->sessionService->get('user_id'),
                'assigned_to' => $data['assigned_to'] ?? null,
                'status' => $data['assigned_to'] ? 'assigned' : 'pending'
            ]);

            // Log activity
            $this->logTaskCreation($taskId, $data['title']);
            
            // Send notifications
            $this->sendTaskNotifications($taskId, $data);
            
            // Assign to intern if specified
            if (!empty($data['assigned_to'])) {
                $this->assignTaskToIntern($taskId, $data['assigned_to']);
            }
            
            // Generate AI feedback suggestions if enabled
            if (defined('ENABLE_AI_SUGGESTIONS') && ENABLE_AI_SUGGESTIONS) {
                $this->generateAIFeedbackSuggestions($taskId, $data);
            }

            // Success message
            $this->sessionService->setFlash('success', 'Task created successfully!');
            
            // Redirect based on action
            $redirectUrl = $this->getRedirectUrl($data);
            header("Location: " . $redirectUrl);
            exit;

        } catch (Exception $e) {
            error_log("Task creation error: " . $e->getMessage());
            $this->sessionService->setFlash('error', 'Failed to create task. Please try again.');
            $this->sessionService->setFlash('form_data', $data);
            header("Location: " . BASE_URL . "/tasks/create");
            exit;
        }
    }

    // LIST ALL TASKS
    public function index() {
        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');
        
        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        $filters = $this->getFiltersFromRequest();
        
        // Get tasks based on role
        if ($role === 'admin') {
            $tasks = $this->taskModel->getAllTasks($page, $perPage, $filters);
            $totalTasks = $this->taskModel->getTotalTasksCount($filters);
        } elseif ($role === 'mentor') {
            $tasks = $this->taskModel->getMentorTasks($userId, $page, $perPage, $filters);
            $totalTasks = $this->taskModel->getMentorTasksCount($userId, $filters);
        } else { // intern
            $tasks = $this->taskModel->getUserTasks($userId, $page, $perPage, $filters);
            $totalTasks = $this->taskModel->getUserTasksCount($userId, $filters);
        }
        
        $totalPages = ceil($totalTasks / $perPage);

        $data = [
            'page_title' => 'Tasks',
            'tasks' => $tasks,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalTasks,
                'per_page' => $perPage
            ],
            'filters' => $filters,
            'statuses' => $this->taskModel->getTaskStatuses(),
            'categories' => $this->taskModel->getTaskCategories(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
                ['label' => 'Tasks', 'url' => BASE_URL . '/tasks', 'active' => true]
            ]
        ];

        require APP_ROOT . '/app/views/tasks/index.php';
    }

    // SHOW SINGLE TASK
    public function show($id) {
        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');
        
        $task = $this->taskModel->findById($id);
        
        if (!$task) {
            $this->sessionService->setFlash('error', 'Task not found');
            header("Location: " . BASE_URL . "/tasks");
            exit;
        }
        
        // Check authorization
        if (!$this->canViewTask($task, $userId, $role)) {
            $this->sessionService->setFlash('error', 'You are not authorized to view this task');
            header("Location: " . BASE_URL . "/tasks");
            exit;
        }
        
        // Get task submissions
        $submissions = $this->taskModel->getTaskSubmissions($id);
        
        // Get task comments/discussions
        $comments = $this->taskModel->getTaskComments($id);
        
        // Get related tasks
        $relatedTasks = $this->taskModel->getRelatedTasks($id);
        
        // Mark as viewed/read
        $this->taskModel->markAsViewed($id, $userId);

        $data = [
            'page_title' => $task['title'],
            'task' => $task,
            'submissions' => $submissions,
            'comments' => $comments,
            'related_tasks' => $relatedTasks,
            'can_edit' => $this->canEditTask($task, $userId, $role),
            'can_submit' => $this->canSubmitToTask($task, $userId, $role),
            'can_review' => $this->canReviewTask($task, $userId, $role),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
                ['label' => 'Tasks', 'url' => BASE_URL . '/tasks'],
                ['label' => $task['title'], 'url' => BASE_URL . '/tasks/' . $id, 'active' => true]
            ]
        ];

        require APP_ROOT . '/app/views/tasks/show.php';
    }

    // EDIT TASK FORM
    public function edit($id) {
        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');
        
        $task = $this->taskModel->findById($id);
        
        if (!$task) {
            $this->sessionService->setFlash('error', 'Task not found');
            header("Location: " . BASE_URL . "/tasks");
            exit;
        }
        
        // Check authorization
        if (!$this->canEditTask($task, $userId, $role)) {
            $this->sessionService->setFlash('error', 'You are not authorized to edit this task');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
        
        // Get available skills
        $skills = $this->skillModel->getAllSkills();
        
        // Get categories
        $categories = $this->taskModel->getTaskCategories();
        
        // Get difficulty levels
        $difficultyLevels = $this->taskModel->getDifficultyLevels();

        $data = [
            'page_title' => 'Edit Task: ' . $task['title'],
            'task' => $task,
            'skills' => $skills,
            'categories' => $categories,
            'difficulty_levels' => $difficultyLevels,
            'form_data' => $this->sessionService->getFlash('form_data') ?? $task,
            'errors' => $this->sessionService->getFlash('errors') ?? [],
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
                ['label' => 'Tasks', 'url' => BASE_URL . '/tasks'],
                ['label' => $task['title'], 'url' => BASE_URL . '/tasks/' . $id],
                ['label' => 'Edit', 'url' => BASE_URL . '/tasks/' . $id . '/edit', 'active' => true]
            ]
        ];

        require APP_ROOT . '/app/views/tasks/edit.php';
    }

    // UPDATE TASK
    public function update($id) {
        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');
        
        $task = $this->taskModel->findById($id);
        
        if (!$task) {
            $this->sessionService->setFlash('error', 'Task not found');
            header("Location: " . BASE_URL . "/tasks");
            exit;
        }
        
        // Check authorization
        if (!$this->canEditTask($task, $userId, $role)) {
            $this->sessionService->setFlash('error', 'You are not authorized to edit this task');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
        
        // Validate CSRF token
        if (!$this->validateCSRFToken()) {
            $this->sessionService->setFlash('error', 'Invalid security token');
            header("Location: " . BASE_URL . "/tasks/" . $id . "/edit");
            exit;
        }
        
        // Sanitize and validate input
        $data = $this->sanitizeTaskData($_POST);
        $validationErrors = $this->validateTaskData($data, true);
        
        if (!empty($validationErrors)) {
            $this->sessionService->setFlash('errors', $validationErrors);
            $this->sessionService->setFlash('form_data', $data);
            header("Location: " . BASE_URL . "/tasks/" . $id . "/edit");
            exit;
        }
        
        // Process skills
        $skills = $this->processSkills($data['required_skills']);
        
        try {
            // Update task
            $updated = $this->taskModel->update($id, [
                'title' => $data['title'],
                'description' => $data['description'],
                'required_skills' => $skills,
                'deadline' => $data['deadline'],
                'estimated_time' => $data['estimated_time'] ?? null,
                'difficulty' => $data['difficulty'] ?? 'intermediate',
                'category' => $data['category'] ?? null,
                'resources' => $data['resources'] ?? null,
                'success_criteria' => $data['success_criteria'] ?? null,
                'status' => $data['status'] ?? $task['status']
            ]);
            
            if ($updated) {
                // Log activity
                $this->logTaskUpdate($id, $data['title']);
                
                // Send update notifications
                $this->sendTaskUpdateNotifications($id, $task['title']);
                
                $this->sessionService->setFlash('success', 'Task updated successfully!');
            } else {
                $this->sessionService->setFlash('error', 'Failed to update task');
            }
            
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
            
        } catch (Exception $e) {
            error_log("Task update error: " . $e->getMessage());
            $this->sessionService->setFlash('error', 'Failed to update task');
            header("Location: " . BASE_URL . "/tasks/" . $id . "/edit");
            exit;
        }
    }

    // DELETE TASK
    public function destroy($id) {
        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');
        
        $task = $this->taskModel->findById($id);
        
        if (!$task) {
            $this->sessionService->setFlash('error', 'Task not found');
            header("Location: " . BASE_URL . "/tasks");
            exit;
        }
        
        // Check authorization
        if (!$this->canDeleteTask($task, $userId, $role)) {
            $this->sessionService->setFlash('error', 'You are not authorized to delete this task');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
        
        // Validate CSRF token
        if (!$this->validateCSRFToken()) {
            $this->sessionService->setFlash('error', 'Invalid security token');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
        
        try {
            $deleted = $this->taskModel->delete($id);
            
            if ($deleted) {
                // Log activity
                $this->logTaskDeletion($id, $task['title']);
                
                $this->sessionService->setFlash('success', 'Task deleted successfully!');
            } else {
                $this->sessionService->setFlash('error', 'Failed to delete task');
            }
            
            header("Location: " . BASE_URL . "/tasks");
            exit;
            
        } catch (Exception $e) {
            error_log("Task deletion error: " . $e->getMessage());
            $this->sessionService->setFlash('error', 'Failed to delete task');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
    }

    // SUBMIT TASK SOLUTION
    public function submit($id) {
        $userId = $this->sessionService->get('user_id');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
        
        $task = $this->taskModel->findById($id);
        
        if (!$task) {
            $this->sessionService->setFlash('error', 'Task not found');
            header("Location: " . BASE_URL . "/tasks");
            exit;
        }
        
        // Check if user can submit
        if (!$this->canSubmitToTask($task, $userId, 'intern')) {
            $this->sessionService->setFlash('error', 'You are not authorized to submit to this task');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
        
        // Validate CSRF token
        if (!$this->validateCSRFToken()) {
            $this->sessionService->setFlash('error', 'Invalid security token');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
        
        $submissionText = $_POST['submission_text'] ?? '';
        $attachment = $_FILES['attachment'] ?? null;
        
        if (empty($submissionText) && empty($attachment['name'])) {
            $this->sessionService->setFlash('error', 'Please provide a submission or attachment');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
        
        try {
            // Handle file upload if present
            $attachmentPath = null;
            if (!empty($attachment['name'])) {
                $attachmentPath = $this->handleFileUpload($attachment, $userId);
            }
            
            // Create submission
            $submissionId = $this->taskModel->createSubmission([
                'task_id' => $id,
                'user_id' => $userId,
                'submission_text' => $submissionText,
                'attachment_path' => $attachmentPath,
                'status' => 'submitted'
            ]);
            
            // Update task status
            $this->taskModel->updateStatus($id, 'submitted');
            
            // Log activity
            $this->logTaskSubmission($id, $userId);
            
            // Send notifications
            $this->sendSubmissionNotifications($id, $userId, $task['created_by']);
            
            // Generate AI feedback if enabled
            if (defined('ENABLE_AI_FEEDBACK') && ENABLE_AI_FEEDBACK) {
                $this->generateAIFeedback($id, $submissionText);
            }
            
            $this->sessionService->setFlash('success', 'Task submitted successfully!');
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
            
        } catch (Exception $e) {
            error_log("Task submission error: " . $e->getMessage());
            $this->sessionService->setFlash('error', 'Failed to submit task: ' . $e->getMessage());
            header("Location: " . BASE_URL . "/tasks/" . $id);
            exit;
        }
    }

    // REVIEW TASK SUBMISSION
    public function review($taskId, $submissionId) {
        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/tasks/" . $taskId);
            exit;
        }
        
        $submission = $this->taskModel->getSubmissionById($submissionId);
        
        if (!$submission) {
            $this->sessionService->setFlash('error', 'Submission not found');
            header("Location: " . BASE_URL . "/tasks/" . $taskId);
            exit;
        }
        
        // Check authorization
        if (!$this->canReviewTask($submission, $userId, $role)) {
            $this->sessionService->setFlash('error', 'You are not authorized to review this submission');
            header("Location: " . BASE_URL . "/tasks/" . $taskId);
            exit;
        }
        
        // Validate CSRF token
        if (!$this->validateCSRFToken()) {
            $this->sessionService->setFlash('error', 'Invalid security token');
            header("Location: " . BASE_URL . "/tasks/" . $taskId);
            exit;
        }
        
        $data = [
            'score' => $_POST['score'] ?? 0,
            'feedback' => $_POST['feedback'] ?? '',
            'status' => $_POST['status'] ?? 'reviewed'
        ];
        
        // Validate score
        if ($data['score'] < 0 || $data['score'] > 100) {
            $this->sessionService->setFlash('error', 'Score must be between 0 and 100');
            header("Location: " . BASE_URL . "/tasks/" . $taskId);
            exit;
        }
        
        try {
            // Update submission
            $this->taskModel->updateSubmission($submissionId, $data);
            
            // Update task status if approved
            if ($data['status'] === 'approved') {
                $this->taskModel->updateStatus($taskId, 'completed');
            }
            
            // Log activity
            $this->logTaskReview($taskId, $submissionId, $userId);
            
            // Send notification to intern
            $this->sendReviewNotification($submission['user_id'], $taskId, $data);
            
            $this->sessionService->setFlash('success', 'Submission reviewed successfully!');
            header("Location: " . BASE_URL . "/tasks/" . $taskId);
            exit;
            
        } catch (Exception $e) {
            error_log("Task review error: " . $e->getMessage());
            $this->sessionService->setFlash('error', 'Failed to review submission');
            header("Location: " . BASE_URL . "/tasks/" . $taskId);
            exit;
        }
    }

    // PRIVATE HELPER METHODS

    private function canCreateTasks() {
        $role = $this->sessionService->get('user_role');
        return in_array($role, ['mentor', 'admin']);
    }

    private function canViewTask($task, $userId, $role) {
        if ($role === 'admin') return true;
        if ($role === 'mentor' && $task['created_by'] == $userId) return true;
        if ($role === 'intern' && $task['assigned_to'] == $userId) return true;
        return false;
    }

    private function canEditTask($task, $userId, $role) {
        if ($role === 'admin') return true;
        if ($role === 'mentor' && $task['created_by'] == $userId) return true;
        return false;
    }

    private function canDeleteTask($task, $userId, $role) {
        if ($role === 'admin') return true;
        if ($role === 'mentor' && $task['created_by'] == $userId) return true;
        return false;
    }

    private function canSubmitToTask($task, $userId, $role) {
        return $role === 'intern' && $task['assigned_to'] == $userId && $task['status'] === 'assigned';
    }

    private function canReviewTask($submission, $userId, $role) {
        if ($role === 'admin') return true;
        if ($role === 'mentor') {
            $task = $this->taskModel->findById($submission['task_id']);
            return $task['created_by'] == $userId;
        }
        return false;
    }

    private function sanitizeTaskData($data) {
        return [
            'title' => htmlspecialchars(trim($data['title'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'required_skills' => $data['required_skills'] ?? '',
            'deadline' => $data['deadline'] ?? '',
            'estimated_time' => $data['estimated_time'] ?? null,
            'difficulty' => $data['difficulty'] ?? 'intermediate',
            'category' => $data['category'] ?? null,
            'resources' => htmlspecialchars(trim($data['resources'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'success_criteria' => htmlspecialchars(trim($data['success_criteria'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'assigned_to' => $data['assigned_to'] ?? null,
            'enhance_with_ai' => isset($data['enhance_with_ai']),
            'status' => $data['status'] ?? 'pending'
        ];
    }

    private function validateTaskData($data, $isUpdate = false) {
        $errors = [];
        
        // Title validation
        if (empty($data['title'])) {
            $errors['title'] = 'Title is required';
        } elseif (strlen($data['title']) > 200) {
            $errors['title'] = 'Title cannot exceed 200 characters';
        }
        
        // Description validation
        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        } elseif (strlen($data['description']) < 20) {
            $errors['description'] = 'Description must be at least 20 characters';
        }
        
        // Skills validation
        if (empty($data['required_skills'])) {
            $errors['required_skills'] = 'At least one skill is required';
        }
        
        // Deadline validation
        if (empty($data['deadline'])) {
            $errors['deadline'] = 'Deadline is required';
        } else {
            $deadline = DateTime::createFromFormat('Y-m-d', $data['deadline']);
            $today = new DateTime();
            
            if (!$deadline || $deadline < $today) {
                $errors['deadline'] = 'Deadline must be a future date';
            }
        }
        
        // Estimated time validation
        if (!empty($data['estimated_time']) && (!is_numeric($data['estimated_time']) || $data['estimated_time'] <= 0)) {
            $errors['estimated_time'] = 'Estimated time must be a positive number';
        }
        
        return $errors;
    }

    private function processSkills($skillsInput) {
        if (is_array($skillsInput)) {
            $skills = $skillsInput;
        } else {
            $skills = explode(',', $skillsInput);
        }
        
        $skills = array_map('trim', $skills);
        $skills = array_filter($skills);
        $skills = array_unique($skills);
        
        // Validate each skill exists in database or create new ones
        $processedSkills = [];
        foreach ($skills as $skill) {
            $skillId = $this->skillModel->findOrCreate($skill);
            if ($skillId) {
                $processedSkills[] = $skillId;
            }
        }
        
        return $processedSkills;
    }

    private function getAITaskSuggestions() {
        if (!defined('ENABLE_AI_SUGGESTIONS') || !ENABLE_AI_SUGGESTIONS) {
            return [];
        }
        
        try {
            $suggestions = $this->aiService->getTaskSuggestions();
            return $suggestions;
        } catch (Exception $e) {
            error_log("AI suggestion error: " . $e->getMessage());
            return [];
        }
    }

    private function enhanceDescriptionWithAI($data) {
        try {
            $enhanced = $this->aiService->enhanceTaskDescription(
                $data['title'],
                $data['description'],
                $data['required_skills']
            );
            return $enhanced ?: $data['description'];
        } catch (Exception $e) {
            error_log("AI enhancement error: " . $e->getMessage());
            return $data['description'];
        }
    }

    private function generateAIFeedbackSuggestions($taskId, $taskData) {
        try {
            $this->aiService->generateFeedbackSuggestions($taskId, $taskData);
        } catch (Exception $e) {
            error_log("AI feedback suggestion error: " . $e->getMessage());
        }
    }

    private function generateAIFeedback($taskId, $submissionText) {
        try {
            $task = $this->taskModel->findById($taskId);
            $feedback = $this->aiService->analyzeTaskSubmission($task['description'], $submissionText);
            
            // Store AI feedback
            $this->taskModel->storeAIFeedback($taskId, $feedback);
        } catch (Exception $e) {
            error_log("AI feedback generation error: " . $e->getMessage());
        }
    }

    private function validateCSRFToken() {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
            return false;
        }
        
        // Clear token after use
        unset($_SESSION['csrf_token']);
        return true;
    }

    private function redirectWithFormData() {
        header("Location: " . BASE_URL . "/tasks/create");
        exit;
    }

    private function getRedirectUrl($data) {
        $action = $_POST['action'] ?? 'save';
        
        switch ($action) {
            case 'save_and_new':
                return BASE_URL . '/tasks/create';
            case 'save_and_assign':
                return BASE_URL . '/tasks/' . $taskId . '/assign';
            default:
                return BASE_URL . '/tasks';
        }
    }

    private function getFiltersFromRequest() {
        return [
            'status' => $_GET['status'] ?? null,
            'category' => $_GET['category'] ?? null,
            'difficulty' => $_GET['difficulty'] ?? null,
            'search' => $_GET['search'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];
    }

    private function handleFileUpload($file, $userId) {
        $uploadDir = APP_ROOT . '/public/uploads/tasks/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validate file
        $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'zip', 'rar', 'jpg', 'jpeg', 'png'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileSize = $file['size'];
        
        if (!in_array($fileExt, $allowedTypes)) {
            throw new Exception('File type not allowed');
        }
        
        if ($fileSize > $maxSize) {
            throw new Exception('File size exceeds limit');
        }
        
        // Generate unique filename
        $filename = $userId . '_' . time() . '_' . uniqid() . '.' . $fileExt;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return '/uploads/tasks/' . $filename;
        } else {
            throw new Exception('Failed to upload file');
        }
    }

    // LOGGING METHODS
    private function logTaskCreation($taskId, $title) {
        $logMessage = sprintf(
            "[%s] Task Created: ID %d - %s by User ID %d",
            date('Y-m-d H:i:s'),
            $taskId,
            $title,
            $this->sessionService->get('user_id')
        );
        error_log($logMessage);
    }

    private function logTaskUpdate($taskId, $title) {
        $logMessage = sprintf(
            "[%s] Task Updated: ID %d - %s by User ID %d",
            date('Y-m-d H:i:s'),
            $taskId,
            $title,
            $this->sessionService->get('user_id')
        );
        error_log($logMessage);
    }

    private function logTaskDeletion($taskId, $title) {
        $logMessage = sprintf(
            "[%s] Task Deleted: ID %d - %s by User ID %d",
            date('Y-m-d H:i:s'),
            $taskId,
            $title,
            $this->sessionService->get('user_id')
        );
        error_log($logMessage);
    }

    private function logTaskSubmission($taskId, $userId) {
        $logMessage = sprintf(
            "[%s] Task Submitted: Task ID %d by User ID %d",
            date('Y-m-d H:i:s'),
            $taskId,
            $userId
        );
        error_log($logMessage);
    }

    private function logTaskReview($taskId, $submissionId, $reviewerId) {
        $logMessage = sprintf(
            "[%s] Task Reviewed: Task ID %d, Submission ID %d by User ID %d",
            date('Y-m-d H:i:s'),
            $taskId,
            $submissionId,
            $reviewerId
        );
        error_log($logMessage);
    }

    // NOTIFICATION METHODS
    private function sendTaskNotifications($taskId, $data) {
        if (!empty($data['assigned_to'])) {
            $this->notificationService->sendTaskAssignedNotification(
                $data['assigned_to'],
                $taskId,
                $data['title']
            );
        }
        
        // Notify admins/mentors about new task
        if (defined('NOTIFY_ON_TASK_CREATION') && NOTIFY_ON_TASK_CREATION) {
            $this->notificationService->sendTaskCreatedNotification($taskId, $data['title']);
        }
    }

    private function sendTaskUpdateNotifications($taskId, $taskTitle) {
        $this->notificationService->sendTaskUpdatedNotification($taskId, $taskTitle);
    }

    private function sendSubmissionNotifications($taskId, $userId, $creatorId) {
        $this->notificationService->sendSubmissionNotification($creatorId, $taskId, $userId);
    }

    private function sendReviewNotification($userId, $taskId, $reviewData) {
        $this->notificationService->sendReviewNotification($userId, $taskId, $reviewData);
    }

    private function assignTaskToIntern($taskId, $internId) {
        $this->taskModel->assignToUser($taskId, $internId);
        
        // Log assignment
        $logMessage = sprintf(
            "[%s] Task Assigned: Task ID %d to User ID %d",
            date('Y-m-d H:i:s'),
            $taskId,
            $internId
        );
        error_log($logMessage);
    }
}