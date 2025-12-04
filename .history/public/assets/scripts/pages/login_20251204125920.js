// Login Page JavaScript

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

    togglePassword.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.textContent = type === 'password' ? '👁️' : '🙈';
    });
}

// Form validation functions
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    return password.length >= 8;
}

function showError(inputId, message) {
    const errorElement = document.getElementById(`${inputId}Error`);
    const inputElement = document.getElementById(inputId);
    if (!errorElement || !inputElement) return;

    errorElement.textContent = message;
    inputElement.style.borderColor = '#DC2626';

    const inputWrapper = inputElement.parentElement;
    inputWrapper.style.animation = 'shake 0.5s';
    setTimeout(() => {
        inputWrapper.style.animation = '';
    }, 500);
}

function clearError(inputId) {
    const errorElement = document.getElementById(`${inputId}Error`);
    const inputElement = document.getElementById(inputId);
    if (!errorElement || !inputElement) return;


    errorElement.textContent = '';
    inputElement.style.borderColor = '#E5E7EB';
}

// Add shake animation dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
`;
document.head.appendChild(style);

// Handle form submission
function initFormSubmission() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    const loginButton = loginForm.querySelector('.btn-login');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const rememberMe = document.getElementById('rememberMe').checked;

        clearError('email');
        clearError('password');

        let hasError = false;

        if (!email) {
            showError('email', 'البريد الإلكتروني مطلوب');
            hasError = true;
        } else if (!validateEmail(email)) {
            showError('email', 'البريد الإلكتروني غير صحيح');
            hasError = true;
        }

        if (!password) {
            showError('password', 'كلمة السر مطلوبة');
            hasError = true;
        } else if (!validatePassword(password)) {
            showError('password', 'كلمة السر يجب أن تكون 8 أحرف على الأقل');
            hasError = true;
        }

        if (hasError) return;

        loginButton.classList.add('loading');
        loginButton.disabled = true;

        try {
            await simulateLogin(email, password, rememberMe);
            showSuccessMessage();
            setTimeout(() => {
                window.location.href = 'donor-dashboard.html';
            }, 1500);
        } catch (error) {
            showError('password', error.message);
            loginButton.classList.remove('loading');
            loginButton.disabled = false;
        }
    });
}

/**
 * Simulates a network request for logging in.
 * @param {string} email - The user's email.
 * @param {string} password - The user's password.
 * @param {boolean} rememberMe - Whether the user wants to be remembered.
 * @returns {Promise<void>} A promise that resolves on successful login or rejects on failure.
 */
function simulateLogin(email, password, rememberMe) {
    console.log(`Login attempt with rememberMe: ${rememberMe}`);
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            // Demo credentials
            if (email === 'test@example.com' && password === 'password123') {
                resolve();
            } else {
                reject(new Error('البريد الإلكتروني أو كلمة السر غير صحيحة'));
            }
        }, 1000); // 1-second delay
    });
}

/**
 * Creates and displays a temporary success message on the screen.
 */
function showSuccessMessage() {
    const messageDiv = document.createElement('div');
    messageDiv.textContent = 'تم تسجيل الدخول بنجاح! جاري التوجيه...';
    messageDiv.style.position = 'fixed';
    messageDiv.style.top = '20px';
    messageDiv.style.left = '50%';
    messageDiv.style.transform = 'translateX(-50%)';
    messageDiv.style.padding = '1rem 2rem';
    messageDiv.style.background = '#34A853';
    messageDiv.style.color = 'white';
    messageDiv.style.borderRadius = '12px';
    messageDiv.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
    messageDiv.style.zIndex = '2000';
    messageDiv.style.opacity = '0';
    messageDiv.style.transition = 'opacity 0.5s';

    document.body.appendChild(messageDiv);

    // Fade in
    setTimeout(() => {
        messageDiv.style.opacity = '1';
    }, 100);

    // Fade out and remove after a delay
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(messageDiv);
        }, 500);
    }, 2500);
}
