// Main Application JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initThemeSystem();
    initNavigation();
    initAnimations();
    initForms();
    initNotifications();
    initLoadingScreen();
    initBackToTop();
    initTooltips();
    initModals();
    
    console.log('AI Internship Manager initialized');
});

// Theme System
function initThemeSystem() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const savedTheme = localStorage.getItem('theme') || 'dark';
    
    // Apply saved theme
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            setTheme(newTheme);
        });
    }
    
    // Theme switcher dropdown
    const themeOptions = document.querySelectorAll('.theme-option');
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const theme = this.dataset.theme;
            setTheme(theme);
        });
    });
}

function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    updateThemeIcon(theme);
    
    // Dispatch theme change event
    document.dispatchEvent(new CustomEvent('themeChange', { detail: { theme } }));
}

function updateThemeIcon(theme) {
    const themeIcon = document.getElementById('themeIcon');
    if (themeIcon) {
        themeIcon.className = theme === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    }
}

// Navigation
function initNavigation() {
    // Navbar scroll effect
    const navbar = document.querySelector('.glass-nav');
    const navProgress = document.getElementById('navProgress');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        // Progress bar
        if (navProgress) {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            navProgress.style.width = scrolled + '%';
        }
    });
    
    // Mobile menu optimization
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            document.body.classList.toggle('menu-open');
        });
    }
    
    // Active link highlighting
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (href !== '/' && currentPath.startsWith(href))) {
            link.classList.add('active');
        }
    });
}

// Animations
function initAnimations() {
    // GSAP animations
    if (typeof gsap !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        
        // Animate elements on scroll
        gsap.utils.toArray('[data-animate]').forEach(element => {
            gsap.from(element, {
                scrollTrigger: {
                    trigger: element,
                    start: 'top 80%',
                    toggleActions: 'play none none reverse'
                },
                y: 50,
                opacity: 0,
                duration: 1,
                ease: 'power3.out'
            });
        });
        
        // Logo animation
        const logo = document.querySelector('.navbar-brand');
        if (logo) {
            gsap.from(logo, {
                duration: 1,
                x: -50,
                opacity: 0,
                ease: 'power3.out'
            });
        }
    }
    
    // AOS initialization
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100,
            disable: window.innerWidth < 768
        });
    }
}

// Forms
function initForms() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Password strength indicator
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            updatePasswordStrength(this, strength);
        });
    });
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea[data-auto-resize]');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
}

function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return Math.min(strength, 5);
}

function updatePasswordStrength(input, strength) {
    const strengthBar = input.parentElement.querySelector('.password-strength');
    if (strengthBar) {
        const width = (strength / 5) * 100;
        strengthBar.style.width = width + '%';
        
        // Update color
        if (strength <= 2) {
            strengthBar.style.background = 'var(--danger-color)';
        } else if (strength <= 4) {
            strengthBar.style.background = 'var(--warning-color)';
        } else {
            strengthBar.style.background = 'var(--success-color)';
        }
    }
}

// Notifications
function initNotifications() {
    const notificationBell = document.getElementById('notificationBell');
    if (!notificationBell) return;
    
    // Toggle notification dropdown
    notificationBell.addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = this.querySelector('.notification-dropdown');
        dropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        const dropdowns = document.querySelectorAll('.notification-dropdown.show');
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    });
    
    // Fetch notifications
    fetchNotifications();
    
    // Auto-refresh every 30 seconds
    setInterval(fetchNotifications, 30000);
}

async function fetchNotifications() {
    try {
        const response = await fetch('/api/notifications');
        const data = await response.json();
        
        updateNotificationUI(data);
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    }
}

function updateNotificationUI(data) {
    const countElement = document.getElementById('notificationCount');
    const listElement = document.querySelector('.notification-list');
    
    if (countElement) {
        countElement.textContent = data.unread_count || 0;
        countElement.style.display = data.unread_count > 0 ? 'block' : 'none';
    }
    
    if (listElement && data.notifications) {
        listElement.innerHTML = data.notifications.map(notification => `
            <div class="notification-item ${notification.unread ? 'unread' : ''}">
                <div class="d-flex">
                    <div class="notification-icon me-3">
                        <i class="bi ${getNotificationIcon(notification.type)}"></i>
                    </div>
                    <div>
                        <p class="small mb-1 fw-semibold">${notification.title}</p>
                        <p class="small text-muted mb-0">${notification.message}</p>
                        <span class="small text-muted">${formatTime(notification.created_at)}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

function getNotificationIcon(type) {
    const icons = {
        task: 'bi-check-circle',
        message: 'bi-chat',
        achievement: 'bi-trophy',
        warning: 'bi-exclamation-triangle',
        info: 'bi-info-circle'
    };
    return icons[type] || 'bi-bell';
}

function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
    if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`;
    return `${Math.floor(diff / 86400000)}d ago`;
}

// Loading Screen
function initLoadingScreen() {
    const loadingScreen = document.getElementById('loadingScreen');
    if (!loadingScreen) return;
    
    // Hide loading screen after page load
    window.addEventListener('load', function() {
        setTimeout(() => {
            loadingScreen.style.opacity = '0';
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 500);
        }, 500);
    });
    
    // Show loading screen before page unload
    window.addEventListener('beforeunload', function() {
        loadingScreen.style.display = 'flex';
        loadingScreen.style.opacity = '1';
    });
}

// Back to Top Button
function initBackToTop() {
    const backToTop = document.getElementById('backToTop');
    if (!backToTop) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });
    
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover'
        });
    });
}

// Modals
function initModals() {
    // Auto-hide success/error messages
    const alerts = document.querySelectorAll('.alert-auto-hide');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Confirmation modals
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export for use in other modules
window.App = {
    setTheme,
    debounce,
    throttle
};