    </div>

    <footer class="text-center text-white py-4 mt-5 glass-footer">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    
</body>
</html>
<style>
    /* Footer Styles */
    .footer-section {
        background: linear-gradient(135deg, 
            rgba(26, 37, 47, 0.95) 0%, 
            rgba(44, 62, 80, 0.95) 100%);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
        margin-top: 80px;
    }

    .footer-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, 
            transparent, 
            var(--accent-color), 
            transparent);
    }

    /* Footer Brand */
    .footer-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .footer-logo-icon {
        font-size: 2.5rem;
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .footer-brand-text {
        font-size: 1.8rem;
        font-weight: 700;
        background: linear-gradient(90deg, var(--light-color), var(--accent-color));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .footer-description {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
        line-height: 1.6;
        max-width: 300px;
    }

    /* Footer Headings */
    .footer-heading {
        color: var(--light-color);
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

    .footer-heading::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background: linear-gradient(90deg, var(--accent-color), transparent);
    }

    /* Footer Links */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 10px;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .footer-links a::before {
        content: '›';
        color: var(--accent-color);
        font-size: 1.2rem;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
    }

    .footer-links a:hover {
        color: var(--light-color);
        transform: translateX(5px);
    }

    .footer-links a:hover::before {
        opacity: 1;
        transform: translateX(0);
    }

    /* Social Icons */
    .footer-social {
        display: flex;
        gap: 15px;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--light-color);
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .social-icon:hover {
        background: var(--accent-color);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(26, 188, 156, 0.3);
        border-color: var(--accent-color);
    }

    /* Newsletter Form */
    .footer-newsletter-text {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .newsletter-form {
        position: relative;
    }

    .newsletter-input {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: var(--light-color);
        padding: 10px 15px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .newsletter-input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.2);
        outline: none;
    }

    .newsletter-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .newsletter-btn {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        border: none;
        border-radius: 10px;
        color: white;
        padding: 10px 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .newsletter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(26, 188, 156, 0.3);
    }

    /* App Badges */
    .app-badges {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .badge-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: var(--light-color);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .badge-link:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--accent-color);
        transform: translateY(-2px);
        color: var(--light-color);
    }

    /* Footer Divider */
    .footer-divider {
        height: 1px;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255, 255, 255, 0.1), 
            transparent);
        margin: 20px 0;
    }

    /* Footer Bottom */
    .footer-bottom {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.85rem;
    }

    .copyright-text {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .footer-legal-links {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
    }

    .footer-legal-links a {
        color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-legal-links a:hover {
        color: var(--accent-color);
    }

    .divider {
        color: rgba(255, 255, 255, 0.3);
    }

    /* Back to Top Button */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        border: none;
        color: white;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(26, 188, 156, 0.3);
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(26, 188, 156, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .footer-brand-text {
            font-size: 1.5rem;
        }
        
        .footer-heading {
            font-size: 1rem;
        }
        
        .footer-legal-links {
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .footer-section {
            text-align: center;
        }
        
        .footer-heading::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .footer-links a::before {
            display: none;
        }
        
        .footer-social {
            justify-content: center;
        }
        
        .footer-description {
            margin: 0 auto;
        }
        
        .back-to-top {
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
        }
    }

    @media (max-width: 576px) {
        .footer-legal-links {
            flex-direction: column;
            gap: 5px;
        }
        
        .divider {
            display: none;
        }
    }
</style>
    <!-- Main content ends here -->

    <!-- Footer Section -->
    <footer class="footer-section">
        <div class="container">
            <!-- Footer content from our previous design -->
            <div class="footer-main py-5">
                <!-- ... footer content ... -->
            </div>
            
            <div class="footer-bottom py-4">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <p class="mb-0 copyright-text">
                            &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-legal-links">
                            <a href="<?php echo BASE_URL; ?>/privacy">Privacy Policy</a>
                            <span class="divider">•</span>
                            <a href="<?php echo BASE_URL; ?>/terms">Terms of Service</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back to Top Button -->
        <button class="back-to-top" id="backToTop">
            <i class="bi bi-chevron-up"></i>
        </button>
    </footer>

    <!-- === EXTERNAL LIBRARIES === -->
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- GSAP for advanced animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- SweetAlert2 for beautiful alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- AOS (Animate On Scroll) -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- === YOUR CUSTOM JS FILES === -->
    <script src="<?php echo BASE_URL; ?>/public/assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/js/animations.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/js/ai-tools.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/js/dashboard-charts.js"></script>
    
    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    </script>
    
    <!-- Page-specific scripts -->
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo BASE_URL . $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline scripts for this page -->
    <?php if (isset($inlineScript)): ?>
        <script>
            <?php echo $inlineScript; ?>
        </script>
    <?php endif; ?>
</body>
</html>
<?php
// End output buffering and send output
ob_end_flush();
?>