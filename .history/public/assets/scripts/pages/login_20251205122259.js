document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
    initFormSubmission();
});

// Toggle password visibility
function initPasswordToggle() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    if (!togglePassword || !passwordInput) return;

    const eyeIcon = togglePassword.querySelector('.eye-icon');
    let isPasswordVisible = false;

    togglePassword.addEventListener('click', () => {
        isPasswordVisible = !isPasswordVisible;
        const type = isPasswordVisible ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        togglePassword.setAttribute('aria-pressed', String(isPasswordVisible));
        eyeIcon.textContent = isPasswordVisible ? '🙈' : '👁️';
    });

    // Allow keyboard navigation (Enter/Space)
    togglePassword.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            togglePassword.click();
        }
    });
}

// Handle form submission with loading state
function initFormSubmission() {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    
    if (!loginForm || !loginBtn) return;

    loginForm.addEventListener('submit', () => {
        // Add loading class to show spinner
        loginBtn.classList.add('loading');
        loginBtn.disabled = true;
    });

    // Reset loading state if there are validation errors
    const hasErrors = document.querySelectorAll('[role="alert"]').length > 0;
    if (hasErrors) {
        loginBtn.classList.remove('loading');
        loginBtn.disabled = false;
    }
}
