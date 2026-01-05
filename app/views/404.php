<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | AI Internship Manager</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #1abc9c;
            --dark-color: #1a252f;
            --light-color: #ecf0f1;
            --danger-color: #e74c3c;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
            color: var(--light-color);
        }

        /* Background Animation */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(52, 152, 219, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(26, 188, 156, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 50%, rgba(231, 76, 60, 0.05) 0%, transparent 50%);
            z-index: -1;
            animation: pulse 20s ease-in-out infinite alternate;
        }

        @keyframes pulse {
            0% { opacity: 0.3; transform: scale(1); }
            100% { opacity: 0.5; transform: scale(1.1); }
        }

        /* Floating Elements */
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(5px);
            z-index: -1;
            animation: float 30s infinite linear;
        }

        .floating-element:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -150px;
            left: -150px;
            background: radial-gradient(circle, rgba(231, 76, 60, 0.1) 0%, transparent 70%);
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -100px;
            right: -100px;
            background: radial-gradient(circle, rgba(52, 152, 219, 0.1) 0%, transparent 70%);
            animation-delay: -10s;
        }

        .floating-element:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 30%;
            right: 20%;
            background: radial-gradient(circle, rgba(26, 188, 156, 0.1) 0%, transparent 70%);
            animation-delay: -20s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(30px, -30px) rotate(90deg); }
            50% { transform: translate(0, -60px) rotate(180deg); }
            75% { transform: translate(-30px, -30px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        /* Glass Card Container */
        .glass-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px) saturate(180%);
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 30px 60px rgba(0, 0, 0, 0.3),
                inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--danger-color), var(--accent-color));
            z-index: 2;
        }

        .glass-container::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(231, 76, 60, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Error Code */
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(90deg, var(--danger-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .error-code::after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
            animation: shine 3s ease-in-out infinite;
        }

        @keyframes shine {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        /* Title and Description */
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--light-color);
        }

        .error-description {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* Icon */
        .error-icon {
            font-size: 4rem;
            color: var(--danger-color);
            margin-bottom: 30px;
            display: inline-block;
            animation: spin 4s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotateY(0deg); }
            100% { transform: rotateY(360deg); }
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all var(--transition-speed) ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
            border: none;
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

        .btn-outline {
            background: transparent;
            color: var(--light-color);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-color);
            transform: translateY(-3px);
        }

        /* Search Bar */
        .search-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 15px;
            margin: 30px 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .search-input {
            width: 100%;
            background: transparent;
            border: none;
            color: var(--light-color);
            font-size: 1rem;
            padding: 10px;
            outline: none;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Navigation Links */
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all var(--transition-speed) ease;
            position: relative;
            padding: 5px 10px;
        }

        .nav-link:hover {
            color: var(--accent-color);
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 1px;
            background: var(--accent-color);
            transition: width var(--transition-speed) ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* 404 Animation */
        .lost-element {
            position: absolute;
            opacity: 0.1;
            pointer-events: none;
            animation: wander 20s infinite linear;
        }

        .lost-element:nth-child(1) {
            content: '404';
            font-size: 10rem;
            font-weight: 900;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .lost-element:nth-child(2) {
            content: '?';
            font-size: 8rem;
            top: 60%;
            right: 15%;
            animation-delay: -5s;
        }

        .lost-element:nth-child(3) {
            content: '🚫';
            font-size: 6rem;
            bottom: 20%;
            left: 15%;
            animation-delay: -10s;
        }

        @keyframes wander {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(100px, -50px) rotate(90deg); }
            50% { transform: translate(50px, 100px) rotate(180deg); }
            75% { transform: translate(-100px, 50px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .glass-container {
                padding: 40px 20px;
                margin: 20px;
            }
            
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .nav-links {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        /* Loading Animation */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Background Floating Elements -->
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>

    <!-- Lost Elements -->
    <div class="lost-element">404</div>
    <div class="lost-element">?</div>
    <div class="lost-element">🚫</div>

    <div class="glass-container">
        <!-- Error Icon -->
        <div class="error-icon">
            <i class="bi bi-search"></i>
        </div>

        <!-- Error Code -->
        <div class="error-code">404</div>

        <!-- Error Title -->
        <h1 class="error-title">Page Not Found</h1>

        <!-- Error Description -->
        <p class="error-description">
            The page you're looking for seems to have wandered off into the digital void. 
            It might have been moved, deleted, or never existed in the first place.
        </p>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search for pages, features, or help..." 
                   onkeypress="handleSearch(event)">
            <div class="search-hint text-white-50 small mt-2" style="opacity: 0.7;">
                Press Enter to search
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?php echo BASE_URL; ?>/" class="btn btn-primary">
                <i class="bi bi-house-door"></i>
                Go Back Home
            </a>
            
            <a href="<?php echo BASE_URL; ?>/dashboard" class="btn btn-outline">
                <i class="bi bi-speedometer2"></i>
                Go to Dashboard
            </a>
            
            <a href="#" onclick="history.back()" class="btn btn-outline">
                <i class="bi bi-arrow-left"></i>
                Go Back
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="nav-links">
            <a href="<?php echo BASE_URL; ?>/help" class="nav-link">
                <i class="bi bi-question-circle"></i>
                Help Center
            </a>
            <a href="<?php echo BASE_URL; ?>/contact" class="nav-link">
                <i class="bi bi-envelope"></i>
                Contact Support
            </a>
            <a href="<?php echo BASE_URL; ?>/status" class="nav-link">
                <i class="bi bi-check-circle"></i>
                System Status
            </a>
            <a href="<?php echo BASE_URL; ?>/sitemap" class="nav-link">
                <i class="bi bi-sitemap"></i>
                Sitemap
            </a>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>AI Internship Manager &copy; <?php echo date('Y'); ?></p>
            <p class="small">Error ID: 404_<?php echo time(); ?></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add typing animation to search placeholder
            const searchInput = document.querySelector('.search-input');
            const placeholders = [
                "Search for pages, features, or help...",
                "Try 'dashboard', 'profile', or 'settings'...",
                "What are you looking for today?"
            ];
            
            let currentPlaceholder = 0;
            let charIndex = 0;
            let isDeleting = false;
            let typingSpeed = 100;
            
            function typePlaceholder() {
                const text = placeholders[currentPlaceholder];
                
                if (isDeleting) {
                    searchInput.placeholder = text.substring(0, charIndex - 1);
                    charIndex--;
                    typingSpeed = 50;
                } else {
                    searchInput.placeholder = text.substring(0, charIndex + 1);
                    charIndex++;
                    typingSpeed = 100;
                }
                
                if (!isDeleting && charIndex === text.length) {
                    isDeleting = true;
                    typingSpeed = 1500; // Pause at end
                } else if (isDeleting && charIndex === 0) {
                    isDeleting = false;
                    currentPlaceholder = (currentPlaceholder + 1) % placeholders.length;
                    typingSpeed = 500; // Pause before typing next
                }
                
                setTimeout(typePlaceholder, typingSpeed);
            }
            
            // Start typing animation only if input is not focused
            searchInput.addEventListener('focus', () => {
                searchInput.placeholder = "Search for pages, features, or help...";
            });
            
            searchInput.addEventListener('blur', () => {
                setTimeout(typePlaceholder, 1000);
            });
            
            setTimeout(typePlaceholder, 1000);
            
            // Add click animation to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.4);
                        transform: scale(0);
                        animation: ripple-animation 0.6s linear;
                        width: ${size}px;
                        height: ${size}px;
                        top: ${y}px;
                        left: ${x}px;
                    `;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
            
            // Add styles for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple-animation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
            
            // Animate elements on load
            const elements = document.querySelectorAll('.error-icon, .error-code, .error-title, .error-description');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
        
        function handleSearch(event) {
            if (event.key === 'Enter') {
                const query = event.target.value.trim();
                if (query) {
                    // Show loading state
                    event.target.style.opacity = '0.5';
                    
                    // Redirect to search or show results
                    setTimeout(() => {
                        alert(`Searching for: "${query}"\n\nIn a real application, this would search the site.`);
                        event.target.style.opacity = '1';
                    }, 500);
                }
            }
        }
    </script>
</body>
</html>