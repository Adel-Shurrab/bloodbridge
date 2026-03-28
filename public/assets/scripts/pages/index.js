// Navbar scroll effect
window.addEventListener('scroll', () => {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Tab switching with smooth animations
const tabBtns = document.querySelectorAll('.tab-btn');
const donorSteps = document.getElementById('donor-steps');
const orgSteps = document.getElementById('org-steps');
let isAnimating = false;

tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        if (isAnimating) return;

        const targetTab = btn.getAttribute('data-tab');
        const isActive = btn.classList.contains('active');

        if (isActive) return;

        // Update active button
        tabBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Determine direction and elements
        const currentActive = document.querySelector('.steps-grid.active');
        const nextActive = targetTab === 'donor' ? donorSteps : orgSteps;

        if (currentActive === nextActive) return;

        isAnimating = true;

        // Slide out current
        const slideOutClass = targetTab === 'donor' ? 'slide-out-right' : 'slide-out-left';
        currentActive.classList.add(slideOutClass);
        currentActive.classList.remove('active');

        // Slide in next after delay
        setTimeout(() => {
            currentActive.classList.remove(slideOutClass);
            nextActive.classList.add('active');

            // Reset cards animation
            const cards = nextActive.querySelectorAll('.step-card');
            cards.forEach(card => {
                card.style.animation = 'none';
                card.offsetHeight; // Trigger reflow
                card.style.animation = null;
            });

            setTimeout(() => {
                isAnimating = false;
            }, 600);
        }, 500);
    });
});

// Smooth scrolling
// document.querySelectorAll('a[href^="#"]').forEach(anchor => {
//     anchor.addEventListener('click', function (e) {
//         const href = this.getAttribute('href');

//         // Skip if it's just a placeholder hash for modal triggers
//         if (href === '#') return;

//         e.preventDefault();
//         const target = document.querySelector(href);
//         if (target) {
//             target.scrollIntoView({ behavior: 'smooth', block: 'start' });
//         }
//     });
// });

// Mobile Menu functionality
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenuCloseBtn = document.getElementById('mobile-menu-close');
const mobileNav = document.getElementById('mobile-nav');
const overlay = document.getElementById('overlay');
const mobileNavLinks = document.querySelectorAll('.mobile-nav-links a');

const openMenu = () => {
    mobileNav.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    mobileMenuBtn.setAttribute('aria-expanded', 'true');
    mobileNav.setAttribute('aria-hidden', 'false');
};

const closeMenu = () => {
    mobileNav.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = ''; // Restore scrolling
    mobileMenuBtn.setAttribute('aria-expanded', 'false');
    mobileNav.setAttribute('aria-hidden', 'true');
};

if (mobileMenuBtn && mobileNav && mobileMenuCloseBtn && overlay) {
    mobileMenuBtn.addEventListener('click', openMenu);
    mobileMenuCloseBtn.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);

    // Close menu when a link is clicked
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', closeMenu);
    });
}

setTimeout(() => {
    document.body.classList.add("loaded");
}, 100);