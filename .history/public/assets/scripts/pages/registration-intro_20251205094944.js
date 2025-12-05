// Registration Intro Page JavaScript

let selectedOption = null;

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
    mobileNav.classList.add('open')re;
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

// Option selection
function initOptionSelection() {
  const donorCard = document.getElementById('donor-card');
  const organizationCard = document.getElementById('organization-card');
  const continueBtn = document.getElementById('continueBtn');

  // Add click event to donor card
  donorCard.addEventListener('click', () => {
    selectOption('donor', donorCard, organizationCard, continueBtn);
  });

  // Add click event to organization card
  organizationCard.addEventListener('click', () => {
    selectOption('organization', organizationCard, donorCard, continueBtn);
  });

  // Add keyboard navigation
  [donorCard, organizationCard].forEach(card => {
    card.setAttribute('tabindex', '0');
    card.addEventListener('keypress', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        card.click();
      }
    });
  });
}

// Select option function
function selectOption(option, selectedCard, otherCard, continueBtn) {
  selectedOption = option;

  // Remove selected class from both cards
  selectedCard.classList.remove('selected');
  otherCard.classList.remove('selected');

  // Add selected class to chosen card with animation
  setTimeout(() => {
    selectedCard.classList.add('selected');
  }, 50);

  // Enable continue button
  continueBtn.disabled = false;

  // Add pulse animation to continue button
  continueBtn.style.animation = 'pulse 0.5s';
  setTimeout(() => {
    continueBtn.style.animation = '';
  }, 500);

  // Store selection in sessionStorage
  sessionStorage.setItem('userType', option);
}

// Handle continue button click
function initContinueButton() {
  const continueBtn = document.getElementById('continueBtn');

  continueBtn.addEventListener('click', () => {
    if (!selectedOption) {
      showToast();
      return;
    }

    // Add loading state
    continueBtn.disabled = true;
    continueBtn.innerHTML = `
            <span>جاري التحميل...</span>
            <div class="spinner"></div>
        `;

    // Simulate loading and redirect
    setTimeout(() => {
      if (selectedOption === 'donor') {
        window.location.href = 'registration-donor.html';
      } else if (selectedOption === 'organization') {
        window.location.href = 'registration-organization.html';
      }
    }, 800);
  });
}

// Show toast notification
function showToast() {
  const toast = document.getElementById('toast');
  toast.classList.add('show');

  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}

// Add hover effects to cards
function initCardHoverEffects() {
  const cards = document.querySelectorAll('.option-card');

  cards.forEach(card => {
    card.addEventListener('mouseenter', function () {
      if (!this.classList.contains('selected')) {
        this.style.transform = 'translateY(-10px) scale(1.02)';
      }
    });

    card.addEventListener('mouseleave', function () {
      if (!this.classList.contains('selected')) {
        this.style.transform = '';
      }
    });
  });
}

// Animate elements on scroll
function initScrollAnimations() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  const elements = document.querySelectorAll('.option-card, .trust-indicators');
  elements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
    observer.observe(el);
  });
}

// Add ripple effect on card click
function createRipple(event) {
  const card = event.currentTarget;
  const ripple = document.createElement('span');

  const rect = card.getBoundingClientRect();
  const size = Math.max(rect.width, rect.height);
  const x = event.clientX - rect.right - size / 2;
  const y = event.clientY - rect.top - size / 2;

  ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        border-radius: 50%;
        background: rgba(211, 47, 47, 0.2);
        right: ${x}px;
        top: ${y}px;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    `;

  card.style.position = 'relative';
  card.style.overflow = 'hidden';
  card.appendChild(ripple);

  setTimeout(() => {
    ripple.remove();
  }, 600);
}

// Add ripple animation stylesheet
function addRippleStyles() {
  const style = document.createElement('style');
  style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    `;
  document.head.appendChild(style);
}

// Track user interaction
function trackInteraction() {
  const cards = document.querySelectorAll('.option-card');

  cards.forEach(card => {
    card.addEventListener('click', (e) => {
      // Add analytics or tracking here if needed
      console.log('Card clicked:', card.dataset.option);

      // Create ripple effect
      createRipple(e);
    });
  });
}

// Preload next pages
function preloadNextPages() {
  const pages = [
    'registration-donor.html',
    'registration-organization.html'
  ];

  pages.forEach(page => {
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = page;
    document.head.appendChild(link);
  });
}

// Check if user is already logged in
function checkLoginStatus() {
  // Check if user has active session
  const isLoggedIn = sessionStorage.getItem('isLoggedIn');

  if (isLoggedIn === 'true') {
    // Redirect to dashboard
    const userType = sessionStorage.getItem('userType');
    if (userType === 'donor') {
      window.location.href = 'donor-dashboard.html';
    } else if (userType === 'organization') {
      window.location.href = 'organization-dashboard.html';
    }
  }
}

// Initialize all functions when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  initNavbarScroll();
  initMobileMenu();
  initOptionSelection();
  initContinueButton();
  initCardHoverEffects();
  initScrollAnimations();
  trackInteraction();
  addRippleStyles();
  preloadNextPages();
  checkLoginStatus();

  // Add entrance animation to page elements
  setTimeout(() => {
    document.body.classList.add('loaded');
  }, 100);
});

// Handle page visibility
document.addEventListener('visibilitychange', () => {
  if (document.hidden) {
    // Page is hidden - pause animations if needed
    console.log('Page hidden');
  } else {
    // Page is visible - resume animations
    console.log('Page visible');
  }
});

// Handle back button
window.addEventListener('popstate', () => {
  // Clear selection when user goes back
  selectedOption = null;
  sessionStorage.removeItem('userType');
});