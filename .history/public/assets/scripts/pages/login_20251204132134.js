document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
    initFormValidation();
    initSubmitButton();
});

// Toggle password visibility
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
    });
}

// Form validation
function initFormValidation() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    if (!form) return;

    // Real-time validation for email
    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            validateEmail(emailInput);
        });

        emailInput.addEventListener('input', () => {
            if (emailInput.classList.contains('input-error')) {
                clearFieldError(emailInput);
            }
        });
    }

    // Real-time validation for password
    if (passwordInput) {
        passwordInput.addEventListener('blur', () => {
            validatePassword(passwordInput);
        });

        passwordInput.addEventListener('input', () => {
            if (passwordInput.classList.contains('input-error')) {
                clearFieldError(passwordInput);
            }
        });
    }

    // Form submission
    form.addEventListener('submit', (e) => {
        if (!validateForm(form)) {
            e.preventDefault();
        }
    });
}

// Validate email
function validateEmail(emailInput) {
    const email = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const errorEl = document.getElementById('emailError');

    if (!email) {
        showFieldError(emailInput, 'البريد الإلكتروني مطلوب', errorEl);
        return false;
    }

    if (!emailRegex.test(email)) {
        showFieldError(emailInput, 'يرجى إدخال بريد إلكتروني صحيح', errorEl);
        return false;
    }

    clearFieldError(emailInput);
    return true;
}

// Validate password
function validatePassword(passwordInput) {
    const password = passwordInput.value;
    const errorEl = document.getElementById('passwordError');

    if (!password) {
        showFieldError(passwordInput, 'كلمة السر مطلوبة', errorEl);
        return false;
    }

    if (password.length < 6) {
        showFieldError(passwordInput, 'كلمة السر يجب أن تكون 6 أحرف على الأقل', errorEl);
        return false;
    }

    clearFieldError(passwordInput);
    return true;
}

// Validate entire form
function validateForm(form) {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    const isEmailValid = validateEmail(emailInput);
    const isPasswordValid = validatePassword(passwordInput);

    return isEmailValid && isPasswordValid;
}

// Show field error
function showFieldError(input, message, errorEl) {
    input.classList.add('input-error');
    if (errorEl) {
        errorEl.textContent = message;
    }
}

// Clear field error
function clearFieldError(input) {
    input.classList.remove('input-error');
    const errorId = input.id + 'Error';
    const errorEl = document.getElementById(errorId);
    if (errorEl) {
        errorEl.textContent = '';
    }
}

// Submit button animation
function initSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('loginForm');

    if (!submitBtn || !form) return;

    form.addEventListener('submit', () => {
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoader = submitBtn.querySelector('.btn-loader');

        submitBtn.disabled = true;
        if (btnText) btnText.style.display = 'none';
        if (btnLoader) {
            btnLoader.style.display = 'inline-block';
            btnLoader.textContent = 'جاري التحقق...';
        }
    });
}