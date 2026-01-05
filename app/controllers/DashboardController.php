<?php

require_once APP_ROOT . '/app/services/SessionService.php';
require_once APP_ROOT . '/app/services/AIService.php';
require_once APP_ROOT . '/app/services/NotificationService.php';
require_once APP_ROOT . '/app/models/Task.php';
require_once APP_ROOT . '/app/models/Progress.php';
require_once APP_ROOT . '/app/models/User.php';

class DashboardController {
    private $sessionService;
    private $aiService;
    private $notificationService;
    private $taskModel;
    private $progressModel;
    private $userModel;

    public function __construct() {
        $this->sessionService = new SessionService();
        $this->aiService = new AIService();
        $this->notificationService = new NotificationService();
        $this->taskModel = new Task();
        $this->progressModel = new Progress();
        $this->userModel = new User();
        
        // Initialize session
        $this->sessionService->start();
    }

    public function index() {
        // 🔐 Protect dashboard - redirect if not logged in
        if (!$this->sessionService->isLoggedIn()) {
            $this->sessionService->setFlash('error', 'Please login to access dashboard');
            $this->sessionService->set('redirect_url', BASE_URL . '/dashboard');
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // Get user data from session
        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');
        $username = $this->sessionService->get('user_name');
        $email = $this->sessionService->get('user_email');

        // Load additional user data from database
        $userProfile = $this->userModel->getUserById($userId);
        
        // Get notifications
        $notifications = $this->notificationService->getUserNotifications($userId, 5);
        $unreadCount = $this->notificationService->getUnreadCount($userId);

        // Prepare dashboard data based on role
        $dashboardData = $this->getDashboardData($userId, $role);
        
        // Get AI recommendations if enabled
        $aiRecommendations = $this->getAIRecommendations($userId, $role);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($userId, $role);
        
        // Get statistics
        $statistics = $this->getStatistics($userId, $role);
        
        // Get upcoming deadlines
        $upcomingDeadlines = $this->getUpcomingDeadlines($userId, $role);

        // Prepare view data
        $data = [
            // User Info
            'user' => [
                'id' => $userId,
                'name' => $username,
                'email' => $email,
                'role' => $role,
                'avatar' => $this->sessionService->get('user_avatar') ?? $this->getDefaultAvatar($username),
                'profile' => $userProfile
            ],
            
            // Dashboard Content
            'dashboard_data' => $dashboardData,
            
            // Notifications
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            
            // AI Features
            'ai_recommendations' => $aiRecommendations,
            
            // Activity & Stats
            'recent_activities' => $recentActivities,
            'statistics' => $statistics,
            'upcoming_deadlines' => $upcomingDeadlines,
            
            // System Info
            'system_stats' => $this->getSystemStats(),
            
            // Quick Actions
            'quick_actions' => $this->getQuickActions($role),
            
            // Performance Metrics
            'performance_metrics' => $this->getPerformanceMetrics($userId, $role),
            
            // Learning Progress
            'learning_progress' => $this->getLearningProgress($userId),
            
            // Timeline
            'timeline' => $this->getTimeline($userId, $role)
        ];

        // Add chart data for dashboard
        $data['chart_data'] = $this->prepareChartData($userId, $role);

        // Add breadcrumb for navigation
        $data['breadcrumbs'] = [
            ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard', 'active' => true]
        ];

        // 🎯 Load role-based dashboard view with layout
        $this->loadDashboardView($role, $data);
    }

    public function overview() {
        // Alias for index
        $this->index();
    }

    public function analytics() {
        if (!$this->sessionService->isLoggedIn()) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');

        if ($role !== 'admin' && $role !== 'mentor') {
            $this->sessionService->setFlash('error', 'Access denied');
            header("Location: " . BASE_URL . "/dashboard");
            exit;
        }

        // Get analytics data
        $analyticsData = $this->getAnalyticsData($userId, $role);
        
        $data = [
            'user' => [
                'id' => $userId,
                'role' => $role,
                'name' => $this->sessionService->get('user_name')
            ],
            'analytics' => $analyticsData,
            'time_period' => $_GET['period'] ?? 'month',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
                ['label' => 'Analytics', 'url' => BASE_URL . '/dashboard/analytics', 'active' => true]
            ]
        ];

        require APP_ROOT . '/app/views/dashboards/analytics.php';
    }

    public function notifications() {
        if (!$this->sessionService->isLoggedIn()) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $userId = $this->sessionService->get('user_id');
        $page = $_GET['page'] ?? 1;
        $perPage = 20;

        $notifications = $this->notificationService->getPaginatedNotifications($userId, $page, $perPage);
        $totalNotifications = $this->notificationService->getTotalNotifications($userId);
        $totalPages = ceil($totalNotifications / $perPage);

        // Mark all as read if requested
        if (isset($_GET['mark_all_read'])) {
            $this->notificationService->markAllAsRead($userId);
            $this->sessionService->setFlash('success', 'All notifications marked as read');
            header("Location: " . BASE_URL . "/dashboard/notifications");
            exit;
        }

        $data = [
            'user' => [
                'id' => $userId,
                'name' => $this->sessionService->get('user_name')
            ],
            'notifications' => $notifications,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalNotifications,
                'per_page' => $perPage
            ],
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
                ['label' => 'Notifications', 'url' => BASE_URL . '/dashboard/notifications', 'active' => true]
            ]
        ];

        require APP_ROOT . '/app/views/dashboards/notifications.php';
    }

    public function markNotificationAsRead($id) {
        if (!$this->sessionService->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $userId = $this->sessionService->get('user_id');
        $success = $this->notificationService->markAsRead($id, $userId);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Notification not found']);
        }
    }

    public function activity() {
        if (!$this->sessionService->isLoggedIn()) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');
        
        $activities = $this->getUserActivityLog($userId, 50);
        
        $data = [
            'user' => [
                'id' => $userId,
                'name' => $this->sessionService->get('user_name'),
                'role' => $role
            ],
            'activities' => $activities,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
                ['label' => 'Activity Log', 'url' => BASE_URL . '/dashboard/activity', 'active' => true]
            ]
        ];

        require APP_ROOT . '/app/views/dashboards/activity.php';
    }

    public function quickStats() {
        if (!$this->sessionService->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $userId = $this->sessionService->get('user_id');
        $role = $this->sessionService->get('user_role');

        $stats = $this->getQuickStats($userId, $role);

        echo json_encode([
            'success' => true,
            'data' => $stats,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    // PRIVATE HELPER METHODS

    private function getDashboardData($userId, $role) {
        switch ($role) {
            case 'intern':
                return $this->getInternDashboardData($userId);
                
            case 'mentor':
                return $this->getMentorDashboardData($userId);
                
            case 'admin':
                return $this->getAdminDashboardData($userId);
                
            default:
                return [];
        }
    }

    private function getInternDashboardData($userId) {
        // Get intern-specific data
        $tasks = $this->taskModel->getUserTasks($userId, 5);
        $completedTasks = $this->taskModel->getCompletedTasks($userId);
        $pendingTasks = $this->taskModel->getPendingTasks($userId);
        $progress = $this->progressModel->getUserProgress($userId);
        $skills = $this->progressModel->getUserSkills($userId);
        $mentors = $this->userModel->getAssignedMentors($userId);

        return [
            'tasks' => $tasks,
            'completed_tasks' => count($completedTasks),
            'pending_tasks' => count($pendingTasks),
            'progress' => $progress,
            'skills' => $skills,
            'mentors' => $mentors,
            'active_tasks' => $this->taskModel->getActiveTasks($userId),
            'upcoming_tasks' => $this->taskModel->getUpcomingTasks($userId, 5),
            'recent_submissions' => $this->taskModel->getRecentSubmissions($userId, 5)
        ];
    }

    private function getMentorDashboardData($userId) {
        // Get mentor-specific data
        $interns = $this->userModel->getAssignedInterns($userId);
        $tasksAssigned = $this->taskModel->getMentorTasks($userId);
        $pendingReviews = $this->taskModel->getPendingReviews($userId);
        $recentFeedback = $this->taskModel->getRecentFeedbackGiven($userId, 5);

        return [
            'interns' => $interns,
            'total_interns' => count($interns),
            'tasks_assigned' => count($tasksAssigned),
            'pending_reviews' => count($pendingReviews),
            'recent_feedback' => $recentFeedback,
            'intern_progress' => $this->getInternsProgress($interns),
            'task_stats' => $this->getMentorTaskStats($userId)
        ];
    }

    private function getAdminDashboardData($userId) {
        // Get admin-specific data
        $totalUsers = $this->userModel->getTotalUsers();
        $activeUsers = $this->userModel->getActiveUsers();
        $newUsers = $this->userModel->getNewUsersThisWeek();
        $systemStats = $this->getSystemStatistics();
        $recentActivities = $this->getSystemActivities(10);

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'system_stats' => $systemStats,
            'recent_activities' => $recentActivities,
            'user_distribution' => $this->getUserDistribution(),
            'platform_stats' => $this->getPlatformStats()
        ];
    }

    private function getAIRecommendations($userId, $role) {
        if (!defined('ENABLE_AI_RECOMMENDATIONS') || !ENABLE_AI_RECOMMENDATIONS) {
            return [];
        }

        try {
            switch ($role) {
                case 'intern':
                    $skills = $this->progressModel->getUserSkills($userId);
                    if (!empty($skills)) {
                        return $this->aiService->getLearningRecommendations($skills);
                    }
                    break;
                    
                case 'mentor':
                    $interns = $this->userModel->getAssignedInterns($userId);
                    if (!empty($interns)) {
                        return $this->aiService->getMentorRecommendations($interns);
                    }
                    break;
            }
        } catch (Exception $e) {
            error_log("AI Recommendations Error: " . $e->getMessage());
        }

        return [];
    }

    private function getRecentActivities($userId, $role) {
        // Get recent activities from activity log
        return $this->userModel->getRecentActivities($userId, 10);
    }

    private function getStatistics($userId, $role) {
        switch ($role) {
            case 'intern':
                return [
                    'total_tasks' => $this->taskModel->getTotalTasks($userId),
                    'completed_tasks' => $this->taskModel->getCompletedTaskCount($userId),
                    'completion_rate' => $this->calculateCompletionRate($userId),
                    'avg_task_score' => $this->taskModel->getAverageScore($userId),
                    'streak_days' => $this->progressModel->getLoginStreak($userId)
                ];
                
            case 'mentor':
                return [
                    'total_interns' => count($this->userModel->getAssignedInterns($userId)),
                    'tasks_assigned' => $this->taskModel->getMentorTaskCount($userId),
                    'pending_reviews' => $this->taskModel->getPendingReviewCount($userId),
                    'avg_rating' => $this->userModel->getMentorRating($userId)
                ];
                
            case 'admin':
                return [
                    'total_users' => $this->userModel->getTotalUsers(),
                    'active_today' => $this->userModel->getActiveUsersToday(),
                    'total_tasks' => $this->taskModel->getTotalSystemTasks(),
                    'platform_usage' => $this->getPlatformUsage()
                ];
                
            default:
                return [];
        }
    }

    private function getUpcomingDeadlines($userId, $role) {
        switch ($role) {
            case 'intern':
                return $this->taskModel->getUpcomingDeadlines($userId, 7); // Next 7 days
                
            case 'mentor':
                return $this->taskModel->getMentorUpcomingDeadlines($userId, 7);
                
            case 'admin':
                return $this->taskModel->getSystemUpcomingDeadlines(7);
                
            default:
                return [];
        }
    }

    private function getSystemStats() {
        return [
            'server_time' => date('Y-m-d H:i:s'),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'php_version' => PHP_VERSION,
            'uptime' => $this->getServerUptime(),
            'last_cron' => $this->getLastCronRun()
        ];
    }

    private function getQuickActions($role) {
        $actions = [
            'common' => [
                ['icon' => 'bi-person', 'label' => 'Profile', 'url' => BASE_URL . '/profile', 'color' => 'primary'],
                ['icon' => 'bi-gear', 'label' => 'Settings', 'url' => BASE_URL . '/settings', 'color' => 'secondary'],
                ['icon' => 'bi-question-circle', 'label' => 'Help', 'url' => BASE_URL . '/help', 'color' => 'info']
            ]
        ];

        switch ($role) {
            case 'intern':
                $actions['role_specific'] = [
                    ['icon' => 'bi-plus-circle', 'label' => 'New Task', 'url' => BASE_URL . '/tasks/create', 'color' => 'success'],
                    ['icon' => 'bi-list-task', 'label' => 'My Tasks', 'url' => BASE_URL . '/tasks', 'color' => 'warning'],
                    ['icon' => 'bi-people', 'label' => 'Mentors', 'url' => BASE_URL . '/mentors', 'color' => 'primary'],
                    ['icon' => 'bi-graph-up', 'label' => 'Progress', 'url' => BASE_URL . '/progress', 'color' => 'info']
                ];
                break;
                
            case 'mentor':
                $actions['role_specific'] = [
                    ['icon' => 'bi-plus-circle', 'label' => 'Assign Task', 'url' => BASE_URL . '/mentor/tasks/create', 'color' => 'success'],
                    ['icon' => 'bi-person-badge', 'label' => 'My Interns', 'url' => BASE_URL . '/mentor/interns', 'color' => 'primary'],
                    ['icon' => 'bi-check-circle', 'label' => 'Review Tasks', 'url' => BASE_URL . '/mentor/reviews', 'color' => 'warning'],
                    ['icon' => 'bi-chat', 'label' => 'Messages', 'url' => BASE_URL . '/messages', 'color' => 'info']
                ];
                break;
                
            case 'admin':
                $actions['role_specific'] = [
                    ['icon' => 'bi-plus-circle', 'label' => 'Add User', 'url' => BASE_URL . '/admin/users/create', 'color' => 'success'],
                    ['icon' => 'bi-people', 'label' => 'All Users', 'url' => BASE_URL . '/admin/users', 'color' => 'primary'],
                    ['icon' => 'bi-bar-chart', 'label' => 'Analytics', 'url' => BASE_URL . '/admin/analytics', 'color' => 'info'],
                    ['icon' => 'bi-shield-check', 'label' => 'Security', 'url' => BASE_URL . '/admin/security', 'color' => 'danger']
                ];
                break;
        }

        return $actions;
    }

    private function getPerformanceMetrics($userId, $role) {
        if ($role !== 'intern') {
            return [];
        }

        return [
            'productivity_score' => $this->progressModel->getProductivityScore($userId),
            'skill_growth' => $this->progressModel->getSkillGrowth($userId, 30), // Last 30 days
            'task_efficiency' => $this->progressModel->getTaskEfficiency($userId),
            'consistency_score' => $this->progressModel->getConsistencyScore($userId)
        ];
    }

    private function getLearningProgress($userId) {
        return $this->progressModel->getLearningPathProgress($userId);
    }

    private function getTimeline($userId, $role) {
        return $this->progressModel->getUserTimeline($userId, 10);
    }

    private function prepareChartData($userId, $role) {
        $chartData = [];

        switch ($role) {
            case 'intern':
                $chartData = [
                    'progress_chart' => $this->progressModel->getProgressChartData($userId),
                    'skill_chart' => $this->progressModel->getSkillChartData($userId),
                    'activity_chart' => $this->progressModel->getActivityChartData($userId, 7)
                ];
                break;
                
            case 'mentor':
                $chartData = [
                    'intern_progress_chart' => $this->getInternProgressChart($userId),
                    'task_distribution_chart' => $this->getTaskDistributionChart($userId),
                    'feedback_chart' => $this->getFeedbackChart($userId, 30)
                ];
                break;
                
            case 'admin':
                $chartData = [
                    'user_growth_chart' => $this->userModel->getUserGrowthChart(12),
                    'platform_usage_chart' => $this->getPlatformUsageChart(),
                    'role_distribution_chart' => $this->getRoleDistributionChart()
                ];
                break;
        }

        return $chartData;
    }

    private function loadDashboardView($role, $data) {
        // Determine view file based on role
        // Existing files are: intern.php, mentor.php, admin.php
        $viewFile = strtolower($role) . '.php';
        $viewPath = APP_ROOT . '/app/views/dashboards/' . $viewFile;
        
        // Check if view exists
        if (!file_exists($viewPath)) {
            die("Dashboard view not found. Please create: " . $viewPath);
        }
        
        // Load layout and view
        require APP_ROOT . '/app/views/layouts/header.php';
        require $viewPath;
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    private function getDefaultAvatar($name) {
        // Generate avatar based on name initials
        $initials = '';
        $words = explode(' ', $name);
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        $initials = substr($initials, 0, 2);
        
        // Generate a color based on name
        $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
        $colorIndex = crc32($name) % count($colors);
        
        return [
            'initials' => $initials,
            'color' => $colors[$colorIndex]
        ];
    }

    private function calculateCompletionRate($userId) {
        $total = $this->taskModel->getTotalTasks($userId);
        $completed = $this->taskModel->getCompletedTaskCount($userId);
        
        if ($total === 0) return 0;
        return round(($completed / $total) * 100, 1);
    }

    private function getServerUptime() {
        // Try to get server uptime
        if (function_exists('shell_exec')) {
            $uptime = @shell_exec('uptime -p');
            if ($uptime) {
                return trim($uptime);
            }
        }
        
        return 'Not available';
    }

    private function getLastCronRun() {
        // Check last cron run from log file
        $cronLog = APP_ROOT . '/logs/cron.log';
        if (file_exists($cronLog)) {
            $lastLine = '';
            $fp = fopen($cronLog, 'r');
            if ($fp) {
                fseek($fp, -1, SEEK_END);
                while (($c = fgetc($fp)) !== false) {
                    if ($c === "\n") {
                        break;
                    }
                    $lastLine = $c . $lastLine;
                    fseek($fp, -2, SEEK_CUR);
                }
                fclose($fp);
                
                if (preg_match('/\[(.*?)\]/', $lastLine, $matches)) {
                    return $matches[1];
                }
            }
        }
        
        return 'Never';
    }

    private function getAnalyticsData($userId, $role) {
        $period = $_GET['period'] ?? 'month';
        $startDate = $this->getPeriodStartDate($period);
        
        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => date('Y-m-d'),
            'metrics' => $this->getAnalyticsMetrics($userId, $role, $startDate),
            'charts' => $this->getAnalyticsCharts($userId, $role, $startDate),
            'top_performers' => $this->getTopPerformers($role, $startDate),
            'trends' => $this->getTrends($role, $period)
        ];
    }

    private function getPeriodStartDate($period) {
        $now = new DateTime();
        
        switch ($period) {
            case 'week':
                return $now->modify('-1 week')->format('Y-m-d');
            case 'month':
                return $now->modify('-1 month')->format('Y-m-d');
            case 'quarter':
                return $now->modify('-3 months')->format('Y-m-d');
            case 'year':
                return $now->modify('-1 year')->format('Y-m-d');
            default: // 30 days
                return $now->modify('-30 days')->format('Y-m-d');
        }
    }

    private function getUserActivityLog($userId, $limit = 50) {
        return $this->userModel->getActivityLog($userId, $limit);
    }

    private function getQuickStats($userId, $role) {
        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'statistics' => $this->getStatistics($userId, $role),
            'notifications' => $this->notificationService->getUnreadCount($userId),
            'upcoming_deadlines' => count($this->getUpcomingDeadlines($userId, $role))
        ];
    }

    // Stub methods for missing functionality
    private function getInternsProgress($interns) { return []; }
    private function getMentorTaskStats($userId) { return []; }
    private function getSystemStatistics() { return []; }
    private function getSystemActivities($limit) { return []; }
    private function getUserDistribution() { return []; }
    private function getPlatformStats() { return []; }
    private function getPlatformUsage() { return 0; }
    private function getInternProgressChart($userId) { return []; }
    private function getTaskDistributionChart($userId) { return []; }
    private function getFeedbackChart($userId, $days) { return []; }
    private function getPlatformUsageChart() { return []; }
    private function getRoleDistributionChart() { return []; }
    private function getAnalyticsMetrics($userId, $role, $startDate) { return []; }
    private function getAnalyticsCharts($userId, $role, $startDate) { return []; }
    private function getTopPerformers($role, $startDate) { return []; }
    private function getTrends($role, $period) { return []; }
}