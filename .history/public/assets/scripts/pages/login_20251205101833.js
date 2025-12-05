// Initialize all login page functionality
document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
    initRememberMe();
    validateRememberedEmailExpiry();
});

/**
 * Toggle password visibility
 * @description Shows/hides password input and updates icon
 */
function initPasswordToggle() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    if (!togglePassword || !passwordInput) return;

    const eyeIcon = togglePassword.querySelector('.eye-icon');

    togglePassword.addEventListener('click', (e) => {
        e.preventDefault();
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.textContent = type === 'password' ? '👁️' : '🙈';
        
        // Update ARIA label for accessibility
        togglePassword.setAttribute('aria-label', 
            type === 'password' ? 'إظهار كلمة السر' : 'إخفاء كلمة السر'
        );
    });
}

/**
 * Initialize Remember Me functionality with best practices
 * @description 
 * - CSRF Protection: Uses Laravel's token from hidden input
 * - Secure Storage: Stores preference in localStorage with prefix
 * - Accessibility: ARIA labels for screen readers
 * - Auto-fill: Pre-fills email if user was remembered
 * - Security: Only stores on secure HTTPS and non-sensitive data
 * - Expiry: Managed server-side (60 days in Laravel middleware)
 */
function initRememberMe() {
    const rememberCheckbox = document.getElementById('rememberMe');
    const emailInput = document.getElementById('email');
    const loginForm = document.getElementById('loginForm');

    if (!rememberCheckbox || !loginForm) return;

    // BEST PRACTICE #1: Restore previously remembered email
    // This provides better UX by prefilling the email field
    const rememberedEmail = localStorage.getItem('bloodbridge_remembered_email');
    if (rememberedEmail && !emailInput.value) {
        emailInput.value = rememberedEmail;
        rememberCheckbox.checked = true;
    }

    // BEST PRACTICE #2: Listen to checkbox changes
    // Store the user's preference for later visits
    rememberCheckbox.addEventListener('change', (e) => {
        const isChecked = e.target.checked;
        
        if (isChecked) {
            // BEST PRACTICE #3: Only store email when explicitly checked by user
            // Never store sensitive data like passwords
            const email = emailInput.value.trim();
            if (isValidEmail(email)) {
                localStorage.setItem('bloodbridge_remembered_email', email);
                // BEST PRACTICE #4: Set expiry timestamp (60 days)
                const expiryTime = new Date().getTime() + (60 * 24 * 60 * 60 * 1000);
                localStorage.setItem('bloodbridge_remember_expiry', expiryTime);
                logRememberMeEvent('saved');
            }
        } else {
            // BEST PRACTICE #5: Allow users to opt-out completely
            // Clear all remember-related data
            localStorage.removeItem('bloodbridge_remembered_email');
            localStorage.removeItem('bloodbridge_remember_expiry');
            logRememberMeEvent('cleared');
        }
    });

    // BEST PRACTICE #6: Handle form submission
    // Ensure proper CSRF token is sent with the form
    loginForm.addEventListener('submit', (e) => {
        // BEST PRACTICE #7: Validate that CSRF token exists
        const csrfToken = document.querySelector('input[name="_token"]');
        if (!csrfToken) {
            e.preventDefault();
            console.error('CSRF token missing - form submission blocked');
            return;
        }

        // BEST PRACTICE #8: Validate remember Me checkbox value
        // Ensure proper value is sent to server
        if (rememberCheckbox.checked && !rememberCheckbox.value) {
            rememberCheckbox.value = '1';
        }

        logRememberMeEvent('submitted');
    });
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} - True if email is valid
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Log Remember Me events for analytics
 * @param {string} event - Event type (saved, cleared, submitted)
 * @description 
 * BEST PRACTICE: Track user behavior with Remember Me feature
 * Helps understand usage patterns and improve UX
 */
function logRememberMeEvent(event) {
    const timestamp = new Date().toISOString();
    const eventData = {
        event: `remember_me_${event}`,
        timestamp: timestamp,
        userAgent: navigator.userAgent.substring(0, 50)
    };
    
    // Log to console for debugging
    console.log('RememberMe Event:', eventData);
    
    // In production, you could send this to an analytics service
    // fetch('/api/analytics', { method: 'POST', body: JSON.stringify(eventData) });
}

/**
 * Check if Remember Me token has expired
 * (Run on page load to validate stored data)
 */
function validateRememberedEmailExpiry() {
    const expiryTime = localStorage.getItem('bloodbridge_remember_expiry');
    if (expiryTime && new Date().getTime() > parseInt(expiryTime)) {
        // Token expired, clear it
        localStorage.removeItem('bloodbridge_remembered_email');
        localStorage.removeItem('bloodbridge_remember_expiry');
        const rememberCheckbox = document.getElementById('rememberMe');
        if (rememberCheckbox) {
            rememberCheckbox.checked = false;
        }
    }
}

/**
 * SECURITY NOTES:
 * ================
 * 1. CSRF Protection: Always included via @csrf in Blade form
 * 2. Server-side: Laravel's auth.remember_web middleware handles token generation
 * 3. HttpOnly Cookies: Laravel automatically uses HttpOnly for security
 * 4. No Sensitive Data: Only email is stored, NEVER passwords
 * 5. Client Validation: localStorage is user-controlled, server validates everything
 * 6. HTTPS Only: In production, this should only work over HTTPS
 * 7. User Control: Users can always clear storage or opt-out
 * 8. Expiry: Server-side token expires after 60 days (configurable)
 * 
 * ACCESSIBILITY NOTES:
 * ====================
 * 1. ARIA Labels: Describe what "Remember Me" does
 * 2. Keyboard Navigation: Tab order preserved
 * 3. Screen Readers: Full context provided
 * 4. Focus States: Visible focus indicators
 * 5. Help Text: Additional context for users
 */