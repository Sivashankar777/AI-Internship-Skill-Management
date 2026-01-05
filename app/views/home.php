<?php 
require_once APP_ROOT . '/app/views/layouts/header.php'; 
?>

<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --accent-color: #1abc9c;
        --dark-color: #1a252f;
        --light-color: #ecf0f1;
        --transition-speed: 0.3s;
    }

    /* Hero Section */
    .hero-section {
        min-height: 100vh;
        background: linear-gradient(135deg, 
            rgba(26, 37, 47, 0.95) 0%, 
            rgba(44, 62, 80, 0.95) 100%),
            url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1950') center/cover;
        position: relative;
        overflow: hidden;
        padding-top: 80px;
    }

    /* Animated Background Elements */
    .particle {
        position: absolute;
        background: rgba(26, 188, 156, 0.1);
        border-radius: 50%;
        animation: float 20s infinite linear;
    }

    .particle:nth-child(1) {
        width: 400px;
        height: 400px;
        top: 10%;
        left: 5%;
        animation-delay: 0s;
        background: radial-gradient(circle, rgba(52, 152, 219, 0.1) 0%, transparent 70%);
    }

    .particle:nth-child(2) {
        width: 300px;
        height: 300px;
        bottom: 10%;
        right: 10%;
        animation-delay: -5s;
        background: radial-gradient(circle, rgba(26, 188, 156, 0.1) 0%, transparent 70%);
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        33% { transform: translateY(-30px) rotate(120deg); }
        66% { transform: translateY(30px) rotate(240deg); }
    }

    /* Glass Card Enhancement */
    .card-3d {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.2),
            inset 0 0 0 1px rgba(255, 255, 255, 0.1);
        transition: all var(--transition-speed) ease;
        position: relative;
        overflow: hidden;
    }

    .card-3d::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
    }

    .card-3d.floating {
        animation: floating-card 6s ease-in-out infinite;
    }

    @keyframes floating-card {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }

    /* Typography */
    .display-3 {
        background: linear-gradient(90deg, var(--light-color), var(--accent-color));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }

    .text-gradient {
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        display: inline-block;
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        border: none;
        border-radius: 50px;
        padding: 15px 40px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all var(--transition-speed) ease;
        position: relative;
        overflow: hidden;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(26, 188, 156, 0.3);
    }

    .btn-primary::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.7s;
    }

    .btn-primary:hover::after {
        left: 100%;
    }

    .btn-outline-light {
        border-width: 2px;
        padding: 15px 40px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all var(--transition-speed) ease;
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-3px);
    }

    /* Progress Bars */
    .progress {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        overflow: hidden;
        height: 12px;
    }

    .progress-bar {
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }

    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        100% { left: 100%; }
    }

    /* Stats Counter */
    .stats-counter {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 15px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        animation: slideIn 1s ease-out 0.5s both;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Features Grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 40px;
    }

    .feature-item {
        text-align: center;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all var(--transition-speed) ease;
    }

    .feature-item:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--accent-color);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .display-3 {
            font-size: 2.5rem;
        }
        
        .btn-primary, .btn-outline-light {
            padding: 12px 30px;
        }
        
        .card-3d {
            margin-top: 40px;
        }
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
        border-radius: 20px;
        padding: 60px;
        margin-top: 80px;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top right, 
            rgba(26, 188, 156, 0.1) 0%, 
            transparent 50%);
    }
</style>

<section class="hero-section">
    <!-- Background Particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    
    <div class="container py-5">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                <h1 class="display-3 fw-bold mb-4">
                    Master Your <span class="text-gradient">Skills</span><br>
                    Launch Your <span class="text-gradient">Career</span>
                </h1>
                <p class="lead mb-4 text-white-50 fs-5">
                    The AI-powered internship management platform that adapts to your growth. 
                    Bridge the gap between learning and doing with intelligent mentorship.
                </p>
                
                <!-- Features List -->
                <div class="d-flex flex-column gap-3 mb-5">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                        <span>Personalized AI-driven learning paths</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                        <span>Real-time progress tracking and analytics</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                        <span>Industry mentor matching system</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                        <span>Automated internship opportunities</span>
                    </div>
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?php echo BASE_URL; ?>/register" class="btn btn-primary btn-lg rounded-pill px-5 py-3">
                        <i class="bi bi-rocket-takeoff me-2"></i> Get Started Free
                    </a>
                    <a href="#features" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3">
                        <i class="bi bi-play-circle me-2"></i> Watch Demo
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="mt-5 pt-4 border-top border-secondary">
                    <p class="text-white-50 mb-3">Trusted by leading companies</p>
                    <div class="d-flex gap-4 align-items-center flex-wrap">
                        <span class="badge bg-primary bg-opacity-20 px-3 py-2 rounded">
                            <i class="bi bi-shield-check me-2"></i>Enterprise Grade Security
                        </span>
                        <span class="badge bg-success bg-opacity-20 px-3 py-2 rounded">
                            <i class="bi bi-graph-up me-2"></i>95% Success Rate
                        </span>
                        <span class="badge bg-info bg-opacity-20 px-3 py-2 rounded">
                            <i class="bi bi-people me-2"></i>10,000+ Users
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 position-relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <div class="card-3d floating p-5 text-center" style="max-width: 500px; margin: 0 auto;">
                    <div class="position-relative z-2">
                        <div class="mb-4">
                            <i class="bi bi-cpu-fill display-6 text-gradient mb-3 d-block"></i>
                            <h3 class="fw-bold mb-1">AI Performance Analysis</h3>
                            <p class="text-white-50">Your personalized skill dashboard</p>
                        </div>
                        
                        <!-- AI Progress Visualizations -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">Python Development</span>
                                <span class="small fw-bold text-accent">85%</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar" style="width: 85%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">Machine Learning</span>
                                <span class="small fw-bold text-accent">72%</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar" style="width: 72%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">Web Development</span>
                                <span class="small fw-bold text-accent">68%</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar" style="width: 68%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">Soft Skills</span>
                                <span class="small fw-bold text-accent">90%</span>
                            </div>
                            <div class="progress mb-4">
                                <div class="progress-bar" style="width: 90%"></div>
                            </div>
                        </div>
                        
                        <div class="p-3 bg-dark bg-opacity-25 rounded">
                            <p class="mb-0">
                                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                                <span class="text-gradient fw-bold">Your readiness score is rising!</span>
                            </p>
                            <small class="text-white-50">+15% improvement this month</small>
                        </div>
                    </div>

                    <!-- Stats Counter -->
                    <div class="stats-counter">
                        <div class="d-flex align-items-center">
                            <div class="text-center me-3">
                                <h4 class="fw-bold mb-0 text-gradient">85%</h4>
                                <small class="text-white-50">Match Rate</small>
                            </div>
                            <div class="vr text-white-50 mx-3"></div>
                            <div class="text-center">
                                <h4 class="fw-bold mb-0 text-gradient">4.9</h4>
                                <small class="text-white-50">Avg Rating</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="text-center position-absolute bottom-0 start-50 translate-middle-x mb-4">
        <a href="#features" class="text-white-50 text-decoration-none">
            <div class="d-flex flex-column align-items-center">
                <span class="mb-2">Explore Features</span>
                <i class="bi bi-chevron-down fs-4 animate-bounce"></i>
            </div>
        </a>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5 bg-dark">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Why Choose AI Internship Manager?</h2>
            <p class="lead text-white-50">Powerful features designed to accelerate your career growth</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-item">
                <i class="bi bi-robot display-6 text-gradient mb-3"></i>
                <h4 class="fw-bold mb-3">AI-Powered Matching</h4>
                <p class="text-white-50">Smart algorithms match you with perfect internships</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-graph-up-arrow display-6 text-gradient mb-3"></i>
                <h4 class="fw-bold mb-3">Progress Analytics</h4>
                <p class="text-white-50">Track your growth with detailed insights</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-person-badge display-6 text-gradient mb-3"></i>
                <h4 class="fw-bold mb-3">Expert Mentors</h4>
                <p class="text-white-50">Connect with industry professionals</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-award display-6 text-gradient mb-3"></i>
                <h4 class="fw-bold mb-3">Certification</h4>
                <p class="text-white-50">Earn verified certificates</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-3">Ready to Transform Your Career?</h2>
                <p class="lead text-white-50 mb-0">Join thousands of successful interns who launched their careers with us.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo BASE_URL; ?>/register" class="btn btn-primary btn-lg rounded-pill px-5 py-3">
                    Start Your Journey <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- AOS Animation Library -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });

    // Floating animation
    document.addEventListener('DOMContentLoaded', function() {
        const card = document.querySelector('.card-3d');
        
        // Add scroll animation for particles
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            document.querySelectorAll('.particle').forEach(particle => {
                particle.style.transform = `translateY(${rate}px) rotate(${rate}deg)`;
            });
        });

        // Animate progress bars on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const progressBars = entry.target.querySelectorAll('.progress-bar');
                    progressBars.forEach(bar => {
                        const width = bar.style.width;
                        bar.style.width = '0';
                        setTimeout(() => {
                            bar.style.transition = 'width 1.5s ease-in-out';
                            bar.style.width = width;
                        }, 300);
                    });
                }
            });
        }, { threshold: 0.5 });

        const progressSection = document.querySelector('.card-3d');
        if (progressSection) observer.observe(progressSection);
    });
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>