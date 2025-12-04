document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
    initFormValidation();
    // We removed initFormSubmission() because Laravel handles the submission now.
});

// Toggle password visibility
function initPasswordToggle() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    if (!togglePassword || !passwordInput) return;

    const eyeIcon = togglePassword.querySelector('.eye-icon');

    togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.textContent = type === 'password' ? '👁️' : '🙈';
    });
}

// Form validation and error handling
function initFormValidation() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    // Add error class if there are validation messages
    if (emailError && emailError.textContent.trim()) {
        emailInput.classList.add('error');
    }

    if (passwordError && passwordError.textContent.trim()) {
        passwordInput.classList.add('error');
    }

    // Remove error class on focus
    if (emailInput) {
        emailInput.addEventListener('focus', () => {
            emailInput.classList.remove('error');
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('focus', () => {
            passwordInput.classList.remove('error');
        });
    }

    // Real-time email validation
    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailInput.value && !emailRegex.test(emailInput.value)) {
                emailInput.classList.add('error');
            }
        });
    }

    // Password validation
    if (passwordInput) {
        passwordInput.addEventListener('blur', () => {
            if (passwordInput.value && passwordInput.value.length < 8) {
                passwordInput.classList.add('error');
            }
        });
    }
}