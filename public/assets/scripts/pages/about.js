// About Page JavaScript

// Navbar scroll effect
function initNavbarScroll() {
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// Mobile Menu functionality
function initMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenuCloseBtn = document.getElementById('mobile-menu-close');
    const mobileNav = document.getElementById('mobile-nav');
    const overlay = document.getElementById('overlay');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-links a');

    const openMenu = () => {
        mobileNav.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    const closeMenu = () => {
        mobileNav.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    if (mobileMenuBtn && mobileNav && mobileMenuCloseBtn && overlay) {
        mobileMenuBtn.addEventListener('click', openMenu);
        mobileMenuCloseBtn.addEventListener('click', closeMenu);
        overlay.addEventListener('click', closeMenu);

        mobileNavLinks.forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    }
}

// Animated Counter for Impact Section
function animateCounter(element, target, duration = 2000) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        const locale = window.appConfig ? window.appConfig.locale : 'en';
        if (current >= target) {
            element.textContent = target.toLocaleString(locale);
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString(locale);
        }
    }, 16);
}

// Intersection Observer for Animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');

                // Trigger counter animation for impact numbers
                if (entry.target.classList.contains('impact-number')) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    animateCounter(entry.target, target);
                }

                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements
    const animateElements = document.querySelectorAll(
        '.mission-card, .value-card, .team-card, .impact-card'
    );

    animateElements.forEach(el => observer.observe(el));

    // Observe impact numbers
    const impactNumbers = document.querySelectorAll('.impact-number');
    impactNumbers.forEach(el => observer.observe(el));
}

// Smooth scroll for anchor links
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Skip if it's just a placeholder hash for modal triggers
            if (href === '#') return;

            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Add staggered animation to cards
function initStaggeredAnimations() {
    const sections = [
        { selector: '.mission-card', delay: 200 },
        { selector: '.value-card', delay: 150 },
        { selector: '.team-card', delay: 200 }
    ];

    sections.forEach(section => {
        const cards = document.querySelectorAll(section.selector);
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * section.delay}ms`;
        });
    });
}

// Parallax effect for hero section
function initParallax() {
    const hero = document.querySelector('.about-hero');

    if (hero) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxSpeed = 0.5;
            hero.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
        });
    }
}

// Add hover effect sound (optional - visual feedback instead)
function initInteractiveEffects() {
    const cards = document.querySelectorAll('.value-card, .team-card, .mission-card');

    cards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transition = 'all 0.3s ease';
        });
    });
}

// Initialize all functions when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initNavbarScroll();
    initMobileMenu();
    initScrollAnimations();
    initSmoothScroll();
    initStaggeredAnimations();
    initParallax();
    initInteractiveEffects();

    // Add entrance animations with delay
    setTimeout(() => {
        document.body.classList.add('loaded');
    }, 100);
});

// Handle window resize
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        // Re-initialize certain functions if needed
        console.log('Window resized');
    }, 250);
});