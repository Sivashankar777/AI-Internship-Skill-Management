<?php
// Start output buffering to capture any output
ob_start();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AI-powered internship management platform">
    <meta name="keywords" content="internship, AI, career, mentorship, learning">
    <meta name="author" content="<?php echo APP_NAME; ?>">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . APP_NAME : APP_NAME . ' - AI Internship Manager'; ?></title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>/public/assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>/public/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>/public/assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo BASE_URL; ?>/public/assets/favicon/site.webmanifest">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- === YOUR CUSTOM CSS FILES === -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/animations.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/responsive.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/themes.css">
    
    <!-- PWA Support -->
    <link rel="manifest" href="<?php echo BASE_URL; ?>/public/assets/favicon/site.webmanifest">
    <meta name="theme-color" content="#2c3e50">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="<?php echo BASE_URL; ?>/public/assets/css/style.css" as="style">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "<?php echo APP_NAME; ?>",
        "description": "AI-powered internship management platform",
        "url": "<?php echo BASE_URL; ?>"
    }
    </script>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-content">
            <div class="loading-spinner">
                <div class="spinner-circle"></div>
                <div class="spinner-circle"></div>
                <div class="spinner-circle"></div>
            </div>
            <div class="loading-text">
                <span class="text-gradient"><?php echo APP_NAME; ?></span>
                <p class="text-white-50">Loading your experience...</p>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg glass-nav fixed-top" id="mainNavbar">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/" aria-label="Go to homepage">
                <div class="brand-wrapper">
                    <i class="bi bi-cpu-fill brand-icon"></i>
                    <span class="brand-text">
                        <span class="text-gradient">AI</span>Internship Manager
                    </span>
                </div>
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon">
                    <i class="bi bi-list"></i>
                </span>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Public Navigation -->
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/">
                                <i class="bi bi-house-door me-2"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/features">
                                <i class="bi bi-stars me-2"></i>Features
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/how-it-works">
                                <i class="bi bi-play-circle me-2"></i>How it Works
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/pricing">
                                <i class="bi bi-tags me-2"></i>Pricing
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/contact">
                                <i class="bi bi-envelope me-2"></i>Contact
                            </a>
                        </li>
                        
                        <!-- Auth Buttons -->
                        <li class="nav-item ms-lg-3 my-2 my-lg-0">
                            <a class="nav-link btn btn-outline-light px-4 rounded-pill" href="<?php echo BASE_URL; ?>/login">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link btn btn-primary px-4 rounded-pill" href="<?php echo BASE_URL; ?>/register">
                                <i class="bi bi-person-plus me-2"></i>Get Started
                            </a>
                        </li>
                    
                    <!-- User Navigation -->
                    <?php else: ?>
                        <?php 
                        $userRole = $_SESSION['user_role'] ?? 'intern';
                        $displayName = $_SESSION['full_name'] ?? 'User';
                        ?>
                        
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/dashboard">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        
                        <!-- Role-based Navigation -->
                        <?php if ($userRole === 'intern'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/tasks">
                                    <i class="bi bi-list-task me-2"></i>Tasks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/mentors">
                                    <i class="bi bi-people me-2"></i>Mentors
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/progress">
                                    <i class="bi bi-graph-up me-2"></i>Progress
                                </a>
                            </li>
                        <?php elseif ($userRole === 'mentor'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/mentor/tasks">
                                    <i class="bi bi-list-check me-2"></i>Manage Tasks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/mentor/interns">
                                    <i class="bi bi-person-badge me-2"></i>My Interns
                                </a>
                            </li>
                        <?php elseif ($userRole === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/dashboard">
                                    <i class="bi bi-shield-check me-2"></i>Admin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/users">
                                    <i class="bi bi-people-fill me-2"></i>Users
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- AI Tools (Common for all roles) -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>/ai-tools">
                                <i class="bi bi-robot me-2"></i>AI Tools
                            </a>
                        </li>
                        
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar me-2">
                                    <?php echo strtoupper(substr($displayName, 0, 1)); ?>
                                </div>
                                <span class="user-name"><?php echo htmlspecialchars($displayName); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/profile">
                                        <i class="bi bi-person me-2"></i>My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/settings">
                                        <i class="bi bi-gear me-2"></i>Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>/help">
                                        <i class="bi bi-question-circle me-2"></i>Help & Support
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Theme Toggle (Desktop) -->
                <div class="theme-toggle ms-lg-4 d-none d-lg-block">
                    <button class="btn btn-sm btn-outline-light" id="themeToggle" aria-label="Toggle theme">
                        <i class="bi bi-sun-fill" id="themeIcon"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="nav-progress" id="navProgress"></div>
    </nav>

    <!-- Notification Bell (Fixed) -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="notification-bell" id="notificationBell">
        <button class="bell-btn" aria-label="Notifications">
            <i class="bi bi-bell"></i>
            <span class="badge" id="notificationCount">0</span>
        </button>
        <div class="notification-dropdown" id="notificationDropdown">
            <div class="notification-header">
                <h6>Notifications</h6>
                <button class="btn btn-sm btn-link mark-all-read">Mark all as read</button>
            </div>
            <div class="notification-list">
                <!-- Notifications will be loaded via AJAX -->
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    <p class="small mb-0 mt-2">Loading notifications...</p>
                </div>
            </div>
            <div class="notification-footer">
                <a href="<?php echo BASE_URL; ?>/notifications" class="btn btn-sm btn-outline-primary w-100">
                    View All Notifications
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content Wrapper -->
    <main class="main-content" id="mainContent">
        <!-- Content will be injected here by views -->