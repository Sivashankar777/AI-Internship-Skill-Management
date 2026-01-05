// Animation JavaScript
class Animations {
    constructor() {
        this.initialized = false;
        this.init();
    }
    
    init() {
        if (this.initialized) return;
        
        this.initGSAP();
        this.initScrollAnimations();
        this.initHoverAnimations();
        this.initPageTransitions();
        this.initParticles();
        this.initTypingEffect();
        this.initCounterAnimations();
        
        this.initialized = true;
    }
    
    initGSAP() {
        if (typeof gsap === 'undefined') {
            console.warn('GSAP not loaded. Animations will be limited.');
            return;
        }
        
        gsap.registerPlugin(ScrollTrigger);
        
        // Animate elements on page load
        this.animateOnLoad();
        
        // Set up ScrollTrigger defaults
        ScrollTrigger.defaults({
            toggleActions: "play none none reverse",
            scroller: window
        });
    }
    
    animateOnLoad() {
        // Animate navbar
        gsap.from('.glass-nav', {
            duration: 1,
            y: -100,
            opacity: 0,
            ease: "power3.out"
        });
        
        // Animate main content
        gsap.from('.main-content', {
            duration: 0.8,
            y: 30,
            opacity: 0,
            delay: 0.3,
            ease: "power3.out"
        });
        
        // Animate cards with stagger
        gsap.from('.card-3d, .glass-card', {
            duration: 1,
            y: 50,
            opacity: 0,
            stagger: 0.1,
            delay: 0.5,
            ease: "power3.out"
        });
        
        // Animate buttons
        gsap.from('.btn-primary', {
            duration: 0.8,
            scale: 0.8,
            opacity: 0,
            stagger: 0.05,
            delay: 0.8,
            ease: "back.out(1.7)"
        });
    }
    
    initScrollAnimations() {
        // Animate elements when they come into view
        const animateElements = document.querySelectorAll('[data-animate]');
        
        animateElements.forEach(element => {
            const animation = element.dataset.animate || 'fade-up';
            const delay = element.dataset.delay || 0;
            
            gsap.from(element, {
                scrollTrigger: {
                    trigger: element,
                    start: "top 85%",
                    end: "bottom 20%",
                    toggleActions: "play none none reverse"
                },
                y: animation.includes('up') ? 50 : animation.includes('down') ? -50 : 0,
                x: animation.includes('left') ? -50 : animation.includes('right') ? 50 : 0,
                opacity: 0,
                duration: 1,
                delay: parseFloat(delay),
                ease: "power3.out"
            });
        });
        
        // Parallax effect for background elements
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            
            gsap.to(element, {
                y: () => ScrollTrigger.maxScroll(window) * speed,
                ease: "none",
                scrollTrigger: {
                    start: 0,
                    end: "max",
                    scrub: true
                }
            });
        });
    }
    
    initHoverAnimations() {
        // Card hover animations
        const cards = document.querySelectorAll('.card-3d, .glass-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', (e) => {
                gsap.to(card, {
                    duration: 0.3,
                    y: -8,
                    rotationX: 2,
                    rotationY: 2,
                    ease: "power2.out",
                    overwrite: true
                });
            });
            
            card.addEventListener('mouseleave', (e) => {
                gsap.to(card, {
                    duration: 0.3,
                    y: 0,
                    rotationX: 0,
                    rotationY: 0,
                    ease: "power2.out",
                    overwrite: true
                });
            });
        });
        
        // Button hover animations
        const buttons = document.querySelectorAll('.btn-primary, .btn-outline-light');
        
        buttons.forEach(button => {
            button.addEventListener('mouseenter', (e) => {
                gsap.to(button, {
                    duration: 0.2,
                    scale: 1.05,
                    ease: "power2.out"
                });
            });
            
            button.addEventListener('mouseleave', (e) => {
                gsap.to(button, {
                    duration: 0.2,
                    scale: 1,
                    ease: "power2.out"
                });
            });
        });
        
        // Nav link hover animations
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            link.addEventListener('mouseenter', (e) => {
                gsap.to(link, {
                    duration: 0.2,
                    y: -2,
                    ease: "power2.out"
                });
            });
            
            link.addEventListener('mouseleave', (e) => {
                gsap.to(link, {
                    duration: 0.2,
                    y: 0,
                    ease: "power2.out"
                });
            });
        });
    }
    
    initPageTransitions() {
        // Smooth page transitions
        const links = document.querySelectorAll('a[href^="/"], a[href^="<?php echo BASE_URL; ?>"]');
        
        links.forEach(link => {
            if (link.target === '_blank' || link.hasAttribute('download')) return;
            
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                
                // Don't intercept if it's a hash link or external
                if (href.includes('#') || href.includes('http')) return;
                
                e.preventDefault();
                
                // Add page transition
                this.fadeOutPage().then(() => {
                    window.location.href = href;
                });
            });
        });
    }
    
    fadeOutPage() {
        return new Promise(resolve => {
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: var(--gradient-primary);
                z-index: 9999;
                opacity: 0;
                pointer-events: none;
            `;
            
            document.body.appendChild(overlay);
            
            gsap.to(overlay, {
                duration: 0.5,
                opacity: 1,
                ease: "power2.in",
                onComplete: () => {
                    setTimeout(resolve, 300);
                }
            });
        });
    }
    
    initParticles() {
        const particleContainer = document.getElementById('particle-container');
        if (!particleContainer) return;
        
        const particleCount = 15;
        
        for (let i = 0; i < particleCount; i++) {
            this.createParticle(particleContainer);
        }
    }
    
    createParticle(container) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const size = Math.random() * 100 + 50;
        const duration = Math.random() * 20 + 10;
        const delay = Math.random() * 5;
        
        particle.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            background: radial-gradient(circle, 
                rgba(99, 102, 241, ${Math.random() * 0.1 + 0.05}) 0%, 
                transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: -1;
        `;
        
        // Random position
        const x = Math.random() * 100;
        const y = Math.random() * 100;
        
        particle.style.left = `${x}%`;
        particle.style.top = `${y}%`;
        
        container.appendChild(particle);
        
        // Animate particle
        gsap.to(particle, {
            duration: duration,
            x: `+=${(Math.random() - 0.5) * 200}`,
            y: `+=${(Math.random() - 0.5) * 200}`,
            rotation: 360,
            ease: "none",
            repeat: -1,
            delay: delay
        });
        
        // Pulsate opacity
        gsap.to(particle, {
            duration: duration / 2,
            opacity: Math.random() * 0.3 + 0.1,
            yoyo: true,
            repeat: -1,
            ease: "sine.inOut",
            delay: delay
        });
    }
    
    initTypingEffect() {
        const typingElements = document.querySelectorAll('[data-typing]');
        
        typingElements.forEach(element => {
            const text = element.textContent;
            element.textContent = '';
            
            const typeWriter = (text, i = 0) => {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                    setTimeout(() => typeWriter(text, i), 50);
                }
            };
            
            // Start typing when element is in view
            ScrollTrigger.create({
                trigger: element,
                start: "top 80%",
                onEnter: () => typeWriter(text)
            });
        });
    }
    
    initCounterAnimations() {
        const counters = document.querySelectorAll('[data-counter]');
        
        counters.forEach(counter => {
            const target = parseInt(counter.dataset.counter);
            const suffix = counter.dataset.suffix || '';
            const prefix = counter.dataset.prefix || '';
            const duration = parseInt(counter.dataset.duration) || 2000;
            
            ScrollTrigger.create({
                trigger: counter,
                start: "top 80%",
                onEnter: () => this.animateCounter(counter, target, suffix, prefix, duration)
            });
        });
    }
    
    animateCounter(element, target, suffix, prefix, duration) {
        let start = 0;
        const increment = target / (duration / 16); // 60fps
        
        const updateCounter = () => {
            start += increment;
            
            if (start < target) {
                element.textContent = prefix + Math.floor(start) + suffix;
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = prefix + target + suffix;
            }
        };
        
        requestAnimationFrame(updateCounter);
    }
    
    // Utility function to check if element is in viewport
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Add ripple effect to elements
    addRippleEffect(selector) {
        const elements = document.querySelectorAll(selector);
        
        elements.forEach(element => {
            element.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.5);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    width: ${size}px;
                    height: ${size}px;
                    top: ${y}px;
                    left: ${x}px;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Add ripple animation CSS if not already present
        if (!document.querySelector('#ripple-animation')) {
            const style = document.createElement('style');
            style.id = 'ripple-animation';
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // Create floating particles
    createFloatingParticles(count = 20) {
        const container = document.createElement('div');
        container.id = 'floating-particles';
        container.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        `;
        
        document.body.appendChild(container);
        
        for (let i = 0; i < count; i++) {
            this.createFloatingParticle(container);
        }
    }
    
    createFloatingParticle(container) {
        const particle = document.createElement('div');
        particle.className = 'floating-particle';
        
        const size = Math.random() * 10 + 5;
        const duration = Math.random() * 10 + 10;
        const delay = Math.random() * 5;
        
        particle.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            background: var(--gradient-primary);
            border-radius: 50%;
            opacity: ${Math.random() * 0.3 + 0.1};
        `;
        
        // Random starting position
        const startX = Math.random() * 100;
        const startY = Math.random() * 100 + 100; // Start below viewport
        
        particle.style.left = `${startX}%`;
        particle.style.top = `${startY}%`;
        
        container.appendChild(particle);
        
        // Animate floating up
        gsap.to(particle, {
            duration: duration,
            y: `-=${window.innerHeight + 100}px`,
            x: `+=${(Math.random() - 0.5) * 100}`,
            rotation: 360,
            ease: "none",
            repeat: -1,
            delay: delay
        });
    }
}

// Initialize animations
document.addEventListener('DOMContentLoaded', () => {
    window.Animations = new Animations();
    
    // Add ripple effect to buttons
    window.Animations.addRippleEffect('.btn-primary, .btn-outline-light');
    
    // Create floating particles
    window.Animations.createFloatingParticles(15);
});

// Animation presets
const AnimationPresets = {
    fadeIn: (element, delay = 0) => {
        gsap.from(element, {
            duration: 1,
            opacity: 0,
            y: 30,
            delay: delay,
            ease: "power3.out"
        });
    },
    
    slideInLeft: (element, delay = 0) => {
        gsap.from(element, {
            duration: 1,
            x: -100,
            opacity: 0,
            delay: delay,
            ease: "power3.out"
        });
    },
    
    slideInRight: (element, delay = 0) => {
        gsap.from(element, {
            duration: 1,
            x: 100,
            opacity: 0,
            delay: delay,
            ease: "power3.out"
        });
    },
    
    scaleIn: (element, delay = 0) => {
        gsap.from(element, {
            duration: 0.8,
            scale: 0.8,
            opacity: 0,
            delay: delay,
            ease: "back.out(1.7)"
        });
    },
    
    staggerChildren: (container, childSelector, delay = 0.1) => {
        const children = container.querySelectorAll(childSelector);
        gsap.from(children, {
            duration: 0.5,
            y: 20,
            opacity: 0,
            stagger: delay,
            ease: "power3.out"
        });
    }
};

// Export animation utilities
window.AnimationPresets = AnimationPresets;