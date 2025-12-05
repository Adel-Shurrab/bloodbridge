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
        // Update aria-pressed state
        togglePassword.setAttribute('aria-pressed', type === 'text' ? 'true' : 'false');
    });
}

// Handle form submission with loading state
function initFormSubmission() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!form || !submitBtn) return;

    form.addEventListener('submit', function(e) {
        // Add loading state to button
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        // Optional: disable all form inputs during submission
        const inputs = form.querySelectorAll('input, button');
        inputs.forEach(input => {
            if (input !== submitBtn) {
                input.disabled = true;
            }
        });
    });
}