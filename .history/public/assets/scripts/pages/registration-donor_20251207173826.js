// Registration Donor Page JavaScript

let currentStep = 1;
const totalSteps = 2;
const formData = {};

// Validate current step
function validateStep(step) {
    let isValid = true;

    if (step === 1) {
        // Personal Information
        // Updated IDs to match Laravel Database Columns
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const nationalId = document.getElementById('national_id').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const birthDate = document.getElementById('birth_date').value;
        const gender = document.getElementById('gender').value;
        const bloodType = document.getElementById('blood_type').value;
        const city = document.getElementById('city').value.trim();
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        // Clear all errors first
        ['name', 'email', 'national_id', 'phone', 'birth_date', 'gender', 'blood_type', 'city', 'password', 'password_confirmation'].forEach(clearError);

        if (!name) {
            showError('name', 'الاسم مطلوب');
            isValid = false;
        }

        if (!email) {
            showError('email', 'البريد الإلكتروني مطلوب');
            isValid = false;
        } else if (!validateEmail(email)) {
            showError('email', 'البريد الإلكتروني غير صحيح');
            isValid = false;
        }

        if (!nationalId) {
            showError('national_id', 'رقم الهوية مطلوب');
            isValid = false;
        } else if (!validateNationalId(nationalId)) {
            showError('national_id', 'رقم الهوية يجب أن يكون 9 أرقام');
            isValid = false;
        }

        if (!phone) {
            showError('phone', 'رقم الجوال مطلوب');
            isValid = false;
        } else if (!validatePhone(phone)) {
            showError('phone', 'رقم الجوال غير صحيح');
            isValid = false;
        }

        if (!birthDate) {
            showError('birth_date', 'تاريخ الميلاد مطلوب');
            isValid = false;
        } else {
            const age = validateAge(birthDate);
            if (age < 18) {
                showError('birth_date', 'يجب أن يكون عمرك 18 سنة على الأقل');
                isValid = false;
            }
        }

        if (!gender) {
            showError('gender', 'الجنس مطلوب');
            isValid = false;
        }

        if (!city) {
            showError('city', 'العنوان مطلوب');
            isValid = false;
        }

        if (!password) {
            showError('password', 'كلمة السر مطلوبة');
            isValid = false;
        } else if (!validatePassword(password)) {
            showError('password', 'كلمة السر يجب أن تكون 8 أحرف على الأقل');
            isValid = false;
        }

        if (!passwordConfirmation) {
            showError('password_confirmation', 'تأكيد كلمة السر مطلوب');
            isValid = false;
        } else if (password !== passwordConfirmation) {
            showError('password_confirmation', 'كلمة السر غير متطابقة');
            isValid = false;
        }

        if (isValid) {
            // Store data for review step
            formData.name = name;
            formData.email = email;
            formData.nationalId = nationalId;
            formData.phone = phone;
            formData.birthDate = birthDate;
            formData.gender = gender;
            formData.city = city;
        }
    } else if (step === 2) {
        // Review & Confirm
        const termsAgree = document.getElementById('termsAgree');
        const errorElement = termsAgree.parentElement.parentElement.querySelector('.error-message');

        if (!termsAgree.checked) {
            if (errorElement) {
                errorElement.textContent = 'يجب الموافقة على الشروط والأحكام';
            }
            isValid = false;
        } else {
            if (errorElement) {
                errorElement.textContent = '';
            }
        }
    }

    return isValid;
}

// Populate review section
function populateReview() {
    const personalInfoReview = document.getElementById('personalInfoReview');

    // Personal Information
    personalInfoReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">الاسم</span>
            <span class="review-value">${formData.name}</span>
        </div>
        <div class="review-item">
            <span class="review-label">البريد الإلكتروني</span>
            <span class="review-value">${formData.email}</span>
        </div>
        <div class="review-item">
            <span class="review-label">رقم الهوية</span>
            <span class="review-value">${formData.nationalId}</span>
        </div>
        <div class="review-item">
            <span class="review-label">رقم الجوال</span>
            <span class="review-value">${formData.phone}</span>
        </div>
        <div class="review-item">
            <span class="review-label">تاريخ الميلاد</span>
            <span class="review-value">${formData.birthDate}</span>
        </div>
        <div class="review-item">
            <span class="review-label">الجنس</span>
            <span class="review-value">${formData.gender === 'male' ? 'ذكر' : 'أنثى'}</span>
        </div>
        <div class="review-item" style="grid-column: 1 / -1;">
            <span class="review-label">العنوان</span>
            <span class="review-value">${formData.city}</span>
        </div>
    `;
}

// Navigation handlers
function initNavigation() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('donorRegistrationForm');

    nextBtn.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
            updateProgressSteps();
        }
    });

    prevBtn.addEventListener('click', () => {
        currentStep--;
        showStep(currentStep);
        updateProgressSteps();
    });

    // IMPORTANT: Form Submission Logic
    submitBtn.addEventListener('click', function (e) {
        e.preventDefault();

        if (!validateStep(currentStep)) {
            return;
        }

        // Show loading
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        // Submit the form to Laravel
        form.submit();
    });
}

// Helper functions (Keep these from your old file)
function validateEmail(email) { const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; return re.test(email); }
function validatePhone(phone) { const re = /^(\+970|0)?[5-9][0-9]{8}$/; return re.test(phone.replace(/\s/g, '')); }
function validateNationalId(id) { return id.length === 9 && /^\d+$/.test(id); }
function validatePassword(password) { return password.length >= 8; }
function validateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) { age--; }
    return age;
}
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (field) {
        const errorElement = field.parentElement.querySelector('.error-message');
        field.classList.add('error');
        if (errorElement) errorElement.textContent = message;
        field.style.animation = 'shake 0.5s';
        setTimeout(() => { field.style.animation = ''; }, 500);
    }
}
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        const errorElement = field.parentElement.querySelector('.error-message');
        field.classList.remove('error');
        if (errorElement) errorElement.textContent = '';
    }
}
function initPasswordToggle() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = button.querySelector('.eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = '🙈';
            } else {
                input.type = 'password';
                icon.textContent = '👁️';
            }
        });
    });
}
function updateProgressSteps() {
    document.querySelectorAll('.step').forEach((step, index) => {
        if (index + 1 < currentStep) {
            step.classList.add('completed');
            step.classList.remove('active');
        } else if (index + 1 === currentStep) {
            step.classList.add('active');
            step.classList.remove('completed');
        } else {
            step.classList.remove('active', 'completed');
        }
    });
}
function showStep(step) {
    document.querySelectorAll('.form-step').forEach((s, index) => {
        s.classList.toggle('active', index + 1 === step);
    });
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    prevBtn.style.display = step === 1 ? 'none' : 'flex';
    if (step === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'flex';
        populateReview();
    } else {
        nextBtn.style.display = 'flex';
        submitBtn.style.display = 'none';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initPasswordToggle();
    // Add shake style
    const style = document.createElement('style');
    style.textContent = `@keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }`;
    document.head.appendChild(style);
});