document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
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