<?php

require_once APP_ROOT . '/app/services/SessionService.php';
require_once APP_ROOT . '/app/services/AIService.php';
require_once APP_ROOT . '/app/services/StatsService.php';
require_once APP_ROOT . '/app/models/Testimonial.php';
require_once APP_ROOT . '/app/models/Feature.php';

class HomeController {
    private $sessionService;
    private $aiService;
    private $statsService;
    private $testimonialModel;
    private $featureModel;

    public function __construct() {
        $this->sessionService = new SessionService();
        $this->aiService = new AIService();
        $this->statsService = new StatsService();
        $this->testimonialModel = new Testimonial();
        $this->featureModel = new Feature();
        
        // Start session if not already started
        $this->sessionService->start();
    }

    public function index() {
        // Get platform statistics
        $stats = $this->getPlatformStats();
        
        // Get testimonials
        $testimonials = $this->getTestimonials();
        
        // Get features
        $features = $this->getFeatures();
        
        // Get AI capabilities showcase
        $aiCapabilities = $this->getAICapabilities();
        
        // Get success stories
        $successStories = $this->getSuccessStories();
        
        // Get pricing plans (if any)
        $pricingPlans = $this->getPricingPlans();
        
        // Check if user is logged in for personalized greeting
        $isLoggedIn = $this->sessionService->isLoggedIn();
        $userName = $isLoggedIn ? $this->sessionService->get('user_name') : null;
        $userRole = $isLoggedIn ? $this->sessionService->get('user_role') : null;
        
        // Prepare hero section data
        $heroData = $this->getHeroData($isLoggedIn, $userName, $userRole);
        
        // Get recent platform updates
        $updates = $this->getRecentUpdates();
        
        // Get partner logos/integrations
        $partners = $this->getPartners();
        
        // Get FAQ data
        $faqs = $this->getFAQs();
        
        // Prepare structured data for SEO
        $structuredData = $this->generateStructuredData();
        
        // Check for referral code
        $referralCode = $_GET['ref'] ?? null;
        if ($referralCode) {
            $this->handleReferral($referralCode);
        }

        // Prepare view data
        $data = [
            // Page metadata
            'page_title' => 'AI Internship Manager - Launch Your Tech Career',
            'page_description' => 'AI-powered platform connecting students with industry mentors, personalized learning paths, and real-world internship opportunities.',
            'page_keywords' => 'internship, AI career platform, tech mentorship, learning, skills development, job placement',
            
            // User context
            'is_logged_in' => $isLoggedIn,
            'user_name' => $userName,
            'user_role' => $userRole,
            
            // Hero section
            'hero' => $heroData,
            
            // Platform statistics
            'stats' => $stats,
            
            // AI capabilities showcase
            'ai_capabilities' => $aiCapabilities,
            
            // Features
            'features' => $features,
            
            // Success stories
            'success_stories' => $successStories,
            
            // Testimonials
            'testimonials' => $testimonials,
            
            // Pricing
            'pricing_plans' => $pricingPlans,
            
            // Updates & News
            'updates' => $updates,
            
            // Partners & Integrations
            'partners' => $partners,
            
            // FAQ
            'faqs' => $faqs,
            
            // SEO
            'structured_data' => $structuredData,
            
            // CTAs
            'ctas' => $this->getCTAs($isLoggedIn),
            
            // Demo video
            'demo_video' => $this->getDemoVideo(),
            
            // Social proof
            'social_proof' => $this->getSocialProof(),
            
            // Events & Webinars
            'events' => $this->getUpcomingEvents(),
            
            // Blog highlights
            'blog_posts' => $this->getBlogHighlights(),
            
            // Newsletter
            'newsletter_status' => $this->getNewsletterStatus(),
            
            // Awards & Recognition
            'awards' => $this->getAwards(),
            
            // Trust badges
            'trust_badges' => $this->getTrustBadges()
        ];

        // Load the home view with data
        $this->loadHomeView($data);
    }

    public function about() {
        $data = [
            'page_title' => 'About Us - AI Internship Manager',
            'page_description' => 'Learn about our mission to bridge the gap between education and industry through AI-powered mentorship.',
            'team' => $this->getTeamMembers(),
            'mission' => $this->getMissionStatement(),
            'values' => $this->getCompanyValues(),
            'timeline' => $this->getCompanyTimeline(),
            'achievements' => $this->getAchievements()
        ];

        require APP_ROOT . '/app/views/pages/about.php';
    }

    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleContactForm();
            return;
        }

        $data = [
            'page_title' => 'Contact Us - AI Internship Manager',
            'page_description' => 'Get in touch with our team for support, partnerships, or inquiries.',
            'departments' => $this->getContactDepartments(),
            'office_locations' => $this->getOfficeLocations(),
            'support_hours' => $this->getSupportHours()
        ];

        require APP_ROOT . '/app/views/pages/contact.php';
    }

    public function features() {
        $data = [
            'page_title' => 'Features - AI Internship Manager',
            'page_description' => 'Discover all the powerful features that make our platform the best choice for your internship journey.',
            'feature_categories' => $this->getFeatureCategories(),
            'comparison_table' => $this->getFeatureComparison(),
            'use_cases' => $this->getUseCases()
        ];

        require APP_ROOT . '/app/views/pages/features.php';
    }

    public function pricing() {
        $data = [
            'page_title' => 'Pricing - AI Internship Manager',
            'page_description' => 'Flexible pricing plans for students, mentors, and organizations.',
            'plans' => $this->getDetailedPricingPlans(),
            'faqs' => $this->getPricingFAQs(),
            'trial_info' => $this->getTrialInformation()
        ];

        require APP_ROOT . '/app/views/pages/pricing.php';
    }

    public function demo() {
        $data = [
            'page_title' => 'Live Demo - AI Internship Manager',
            'page_description' => 'Experience our platform with a guided tour and interactive demo.',
            'demo_steps' => $this->getDemoSteps(),
            'demo_features' => $this->getDemoFeatures(),
            'schedule_link' => $this->getScheduleLink()
        ];

        require APP_ROOT . '/app/views/pages/demo.php';
    }

    public function blog() {
        $page = $_GET['page'] ?? 1;
        $category = $_GET['category'] ?? null;
        
        $data = [
            'page_title' => 'Blog - AI Internship Manager',
            'page_description' => 'Insights, tips, and news about internships, career development, and AI in education.',
            'posts' => $this->getBlogPosts($page, $category),
            'categories' => $this->getBlogCategories(),
            'popular_posts' => $this->getPopularPosts(),
            'pagination' => $this->getBlogPagination($page, $category)
        ];

        require APP_ROOT . '/app/views/pages/blog.php';
    }

    public function sitemap() {
        header('Content-Type: application/xml');
        
        $sitemap = $this->generateSitemap();
        
        echo $sitemap;
        exit;
    }

    public function privacy() {
        $data = [
            'page_title' => 'Privacy Policy - AI Internship Manager',
            'page_description' => 'Learn how we protect your data and privacy.',
            'last_updated' => '2024-01-15',
            'policy_sections' => $this->getPrivacyPolicySections()
        ];

        require APP_ROOT . '/app/views/pages/privacy.php';
    }

    public function terms() {
        $data = [
            'page_title' => 'Terms of Service - AI Internship Manager',
            'page_description' => 'Terms and conditions for using our platform.',
            'last_updated' => '2024-01-15',
            'terms_sections' => $this->getTermsSections()
        ];

        require APP_ROOT . '/app/views/pages/terms.php';
    }

    public function newsletterSubscribe() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL);
            exit;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sessionService->setFlash('error', 'Please enter a valid email address');
            header("Location: " . BASE_URL);
            exit;
        }

        $result = $this->subscribeToNewsletter($email, $name);

        if ($result['success']) {
            $this->sessionService->setFlash('success', 'Successfully subscribed to our newsletter!');
        } else {
            $this->sessionService->setFlash('error', $result['message']);
        }

        header("Location: " . BASE_URL);
        exit;
    }

    // PRIVATE HELPER METHODS

    private function loadHomeView($data) {
        // Set page title for layout
        $pageTitle = $data['page_title'] ?? 'AI Internship Manager';
        
        // Include header with data
        require APP_ROOT . '/app/views/layouts/header.php';
        
        // Include home view
        require APP_ROOT . '/app/views/home.php';
        
        // Include footer
        require APP_ROOT . '/app/views/layouts/footer.php';
    }

    private function getPlatformStats() {
        return $this->statsService->getPlatformStatistics();
    }

    private function getTestimonials() {
        return $this->testimonialModel->getFeaturedTestimonials(6);
    }

    private function getFeatures() {
        return $this->featureModel->getAllFeatures();
    }

    private function getAICapabilities() {
        return [
            [
                'title' => 'Skill Gap Analysis',
                'description' => 'AI identifies missing skills and creates personalized learning paths',
                'icon' => 'bi-graph-up',
                'color' => 'primary'
            ],
            [
                'title' => 'Resume Optimization',
                'description' => 'ATS-friendly resume analysis and improvement suggestions',
                'icon' => 'bi-file-earmark-text',
                'color' => 'success'
            ],
            [
                'title' => 'Interview Preparation',
                'description' => 'Personalized interview questions based on target roles',
                'icon' => 'bi-chat-square-text',
                'color' => 'warning'
            ],
            [
                'title' => 'Task Feedback',
                'description' => 'Instant AI feedback on submitted tasks and projects',
                'icon' => 'bi-lightbulb',
                'color' => 'info'
            ]
        ];
    }

    private function getSuccessStories() {
        return [
            [
                'name' => 'Sarah Chen',
                'role' => 'Software Engineer at Google',
                'story' => 'Landed my dream job at Google after completing AI-recommended learning path',
                'avatar' => 'SC',
                'duration' => '3 months'
            ],
            [
                'name' => 'Marcus Johnson',
                'role' => 'Full Stack Developer at Airbnb',
                'story' => 'Platform matched me with the perfect mentor who guided me through complex projects',
                'avatar' => 'MJ',
                'duration' => '4 months'
            ],
            [
                'name' => 'Priya Patel',
                'role' => 'Data Scientist at Microsoft',
                'story' => 'AI resume review helped me get 3x more interview calls',
                'avatar' => 'PP',
                'duration' => '2 months'
            ]
        ];
    }

    private function getPricingPlans() {
        return [
            [
                'name' => 'Starter',
                'price' => 'Free',
                'period' => 'forever',
                'features' => ['Basic skill analysis', 'Limited AI tools', 'Community access', '5 tasks/month'],
                'button_text' => 'Get Started',
                'button_color' => 'outline-primary'
            ],
            [
                'name' => 'Pro',
                'price' => '$19',
                'period' => 'per month',
                'features' => ['Advanced AI analysis', 'Unlimited tasks', 'Mentor matching', 'Priority support'],
                'button_text' => 'Try Pro Free',
                'button_color' => 'primary',
                'popular' => true
            ],
            [
                'name' => 'Team',
                'price' => '$99',
                'period' => 'per month',
                'features' => ['Everything in Pro', 'Team management', 'Custom onboarding', 'API access'],
                'button_text' => 'Contact Sales',
                'button_color' => 'outline-primary'
            ]
        ];
    }

    private function getHeroData($isLoggedIn, $userName, $userRole) {
        if ($isLoggedIn && $userName) {
            return [
                'title' => "Welcome back, {$userName}!",
                'subtitle' => "Ready to continue your journey to becoming a {$userRole}?",
                'show_cta' => false,
                'dashboard_link' => BASE_URL . '/dashboard'
            ];
        }

        return [
            'title' => 'Master Your <span class="text-gradient">Skills</span><br>Launch Your <span class="text-gradient">Career</span>',
            'subtitle' => 'AI-powered internship management platform that adapts to your growth. Bridge the gap between learning and doing with intelligent mentorship.',
            'show_cta' => true,
            'primary_cta' => [
                'text' => 'Get Started Free',
                'url' => BASE_URL . '/register',
                'icon' => 'bi-rocket-takeoff'
            ],
            'secondary_cta' => [
                'text' => 'Watch Demo',
                'url' => '#demo',
                'icon' => 'bi-play-circle'
            ]
        ];
    }

    private function getRecentUpdates() {
        return [
            [
                'date' => '2024-01-15',
                'title' => 'New AI Mentor Matching Algorithm',
                'description' => 'Improved matching accuracy by 40%'
            ],
            [
                'date' => '2024-01-10',
                'title' => 'Mobile App Released',
                'description' => 'Now available on iOS and Android'
            ],
            [
                'date' => '2024-01-05',
                'title' => '1000+ New Learning Resources',
                'description' => 'Added courses from top universities'
            ]
        ];
    }

    private function getPartners() {
        return [
            ['name' => 'Google', 'logo' => 'google.svg'],
            ['name' => 'Microsoft', 'logo' => 'microsoft.svg'],
            ['name' => 'IBM', 'logo' => 'ibm.svg'],
            ['name' => 'AWS', 'logo' => 'aws.svg'],
            ['name' => 'GitHub', 'logo' => 'github.svg']
        ];
    }

    private function getFAQs() {
        return [
            [
                'question' => 'How does the AI mentorship work?',
                'answer' => 'Our AI analyzes your skills, goals, and learning style to match you with the perfect mentor and create personalized learning paths.'
            ],
            [
                'question' => 'Is there a free trial?',
                'answer' => 'Yes! You can try all Pro features free for 14 days. No credit card required.'
            ],
            [
                'question' => 'What kind of internships are available?',
                'answer' => 'We offer internships in software development, data science, UX/UI design, cybersecurity, and more.'
            ],
            [
                'question' => 'How long does it take to get matched with a mentor?',
                'answer' => 'Most users get matched within 24-48 hours after completing their profile.'
            ]
        ];
    }

    private function generateStructuredData() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => 'AI Internship Manager',
            'url' => BASE_URL,
            'description' => 'AI-powered platform connecting students with industry mentors and internship opportunities',
            'applicationCategory' => 'EducationalApplication',
            'operatingSystem' => 'Any',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD'
            ]
        ];
    }

    private function handleReferral($referralCode) {
        // Store referral code in session for registration
        $_SESSION['referral_code'] = $referralCode;
        
        // Track referral view
        $this->statsService->trackReferralView($referralCode);
    }

    private function getCTAs($isLoggedIn) {
        if ($isLoggedIn) {
            return [
                [
                    'text' => 'Go to Dashboard',
                    'url' => BASE_URL . '/dashboard',
                    'style' => 'primary',
                    'icon' => 'bi-speedometer2'
                ],
                [
                    'text' => 'Explore AI Tools',
                    'url' => BASE_URL . '/ai-tools',
                    'style' => 'outline-light',
                    'icon' => 'bi-robot'
                ]
            ];
        }

        return [
            [
                'text' => 'Start Free Trial',
                'url' => BASE_URL . '/register',
                'style' => 'primary',
                'icon' => 'bi-rocket-takeoff'
            ],
            [
                'text' => 'Schedule Demo',
                'url' => BASE_URL . '/demo',
                'style' => 'outline-light',
                'icon' => 'bi-calendar'
            ],
            [
                'text' => 'View Pricing',
                'url' => BASE_URL . '/pricing',
                'style' => 'light',
                'icon' => 'bi-tags'
            ]
        ];
    }

    private function getDemoVideo() {
        return [
            'url' => 'https://www.youtube.com/embed/demo-video-id',
            'title' => 'See AI Internship Manager in Action',
            'description' => '3-minute tour of our platform features'
        ];
    }

    private function getSocialProof() {
        return [
            'rating' => 4.9,
            'reviews' => 1247,
            'downloads' => '10K+',
            'countries' => 85,
            'satisfaction' => '98%'
        ];
    }

    private function getUpcomingEvents() {
        return [
            [
                'date' => '2024-02-01',
                'title' => 'Tech Career Summit 2024',
                'description' => 'Virtual conference with industry leaders',
                'link' => '#'
            ],
            [
                'date' => '2024-02-15',
                'title' => 'AI in Education Webinar',
                'description' => 'Learn how AI is transforming learning',
                'link' => '#'
            ]
        ];
    }

    private function getBlogHighlights() {
        return [
            [
                'title' => 'Top 10 Skills for 2024',
                'excerpt' => 'Discover the most in-demand tech skills for the coming year',
                'read_time' => '5 min',
                'link' => BASE_URL . '/blog/top-skills-2024'
            ],
            [
                'title' => 'How to Ace Technical Interviews',
                'excerpt' => 'Pro tips from our hiring partners',
                'read_time' => '8 min',
                'link' => BASE_URL . '/blog/technical-interview-tips'
            ]
        ];
    }

    private function getNewsletterStatus() {
        return [
            'subscribers' => '50,000+',
            'frequency' => 'Weekly',
            'next_issue' => 'Tomorrow'
        ];
    }

    private function getAwards() {
        return [
            ['name' => 'EdTech Innovation Award 2023', 'organization' => 'TechEd Magazine'],
            ['name' => 'Best AI Platform 2023', 'organization' => 'AI Excellence Awards']
        ];
    }

    private function getTrustBadges() {
        return [
            ['name' => 'GDPR Compliant', 'icon' => 'bi-shield-check'],
            ['name' => 'SOC 2 Certified', 'icon' => 'bi-award'],
            ['name' => '256-bit SSL Encryption', 'icon' => 'bi-lock']
        ];
    }

    private function handleContactForm() {
        // Validate CSRF token
        if (!$this->validateCSRFToken()) {
            $this->sessionService->setFlash('error', 'Invalid security token');
            header("Location: " . BASE_URL . "/contact");
            exit;
        }

        $data = [
            'name' => htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'),
            'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
            'subject' => htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'),
            'department' => htmlspecialchars($_POST['department'] ?? '', ENT_QUOTES, 'UTF-8')
        ];

        // Validate
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            $this->sessionService->setFlash('error', 'Please fill all required fields');
            $this->sessionService->setFlash('form_data', $data);
            header("Location: " . BASE_URL . "/contact");
            exit;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->sessionService->setFlash('error', 'Please enter a valid email address');
            $this->sessionService->setFlash('form_data', $data);
            header("Location: " . BASE_URL . "/contact");
            exit;
        }

        // Send email (implement your email service)
        $sent = $this->sendContactEmail($data);

        if ($sent) {
            $this->sessionService->setFlash('success', 'Message sent successfully! We\'ll get back to you within 24 hours.');
            header("Location: " . BASE_URL . "/contact");
            exit;
        } else {
            $this->sessionService->setFlash('error', 'Failed to send message. Please try again later.');
            $this->sessionService->setFlash('form_data', $data);
            header("Location: " . BASE_URL . "/contact");
            exit;
        }
    }

    private function validateCSRFToken() {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        return !empty($token) && hash_equals($sessionToken, $token);
    }

    private function subscribeToNewsletter($email, $name) {
        // Implement newsletter subscription logic
        // This could integrate with Mailchimp, SendGrid, etc.
        
        try {
            // Simulate subscription
            $success = true; // Replace with actual subscription logic
            
            if ($success) {
                return ['success' => true, 'message' => 'Subscribed successfully!'];
            } else {
                return ['success' => false, 'message' => 'Subscription failed. Please try again.'];
            }
        } catch (Exception $e) {
            error_log("Newsletter subscription error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred. Please try again later.'];
        }
    }

    private function generateSitemap() {
        $baseUrl = BASE_URL;
        $currentDate = date('Y-m-d');
        
        $urls = [
            ['loc' => $baseUrl, 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => $baseUrl . '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/features', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/pricing', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => $baseUrl . '/blog', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => $baseUrl . '/privacy', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => $baseUrl . '/terms', 'priority' => '0.3', 'changefreq' => 'yearly']
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $currentDate . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function sendContactEmail($data) {
        // Implement email sending logic
        // This could use PHPMailer, SendGrid, etc.
        
        try {
            // Simulate email sending
            $to = defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'contact@example.com';
            $subject = "Contact Form: {$data['subject']}";
            $message = "Name: {$data['name']}\n";
            $message .= "Email: {$data['email']}\n";
            $message .= "Department: {$data['department']}\n\n";
            $message .= "Message:\n{$data['message']}\n";
            
            $headers = "From: {$data['email']}\r\n";
            $headers .= "Reply-To: {$data['email']}\r\n";
            
            // In production, use proper email library
            // return mail($to, $subject, $message, $headers);
            
            return true; // Simulate success
        } catch (Exception $e) {
            error_log("Contact email error: " . $e->getMessage());
            return false;
        }
    }

    // Additional methods for other pages...
    private function getTeamMembers() { return []; }
    private function getMissionStatement() { return ''; }
    private function getCompanyValues() { return []; }
    private function getCompanyTimeline() { return []; }
    private function getAchievements() { return []; }
    private function getContactDepartments() { return []; }
    private function getOfficeLocations() { return []; }
    private function getSupportHours() { return []; }
    private function getFeatureCategories() { return []; }
    private function getFeatureComparison() { return []; }
    private function getUseCases() { return []; }
    private function getDetailedPricingPlans() { return []; }
    private function getPricingFAQs() { return []; }
    private function getTrialInformation() { return []; }
    private function getDemoSteps() { return []; }
    private function getDemoFeatures() { return []; }
    private function getScheduleLink() { return '#'; }
    private function getBlogPosts($page, $category) { return []; }
    private function getBlogCategories() { return []; }
    private function getPopularPosts() { return []; }
    private function getBlogPagination($page, $category) { return []; }
    private function getPrivacyPolicySections() { return []; }
    private function getTermsSections() { return []; }
}