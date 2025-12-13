// Registration Donor Page JavaScript

let currentStep = 1;
const totalSteps = 2;
const formData = {};

// Form validation functions
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^(\+970|0)?[5-9][0-9]{8}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

function validateNationalId(id) {
    return id.length === 9 && /^\d+$/.test(id);
}

function validatePassword(password) {
    return password.length >= 8;
}

function validateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    const age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        return age - 1;
    }
    return age;
}

// Show error message
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const errorElement = field.closest('.form-group')?.querySelector('.error-message');

    field.classList.add('error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }

    // Shake animation
    field.style.animation = 'none';
    setTimeout(() => {
        field.style.animation = 'shake 0.5s';
    }, 10);
}

// Clear error message
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const errorElement = field.closest('.form-group')?.querySelector('.error-message');

    field.classList.remove('error');
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.classList.remove('show');
    }
}

// Add shake animation if not already in styles
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
`;
if (!document.querySelector('style:contains("shake")')) {
    document.head.appendChild(shakeStyle);
}

// Validate current step
function validateStep(step) {
    let isValid = true;

    if (step === 1) {
        // Validate Personal Information
        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const nationalId = document.getElementById('national_id');
        const phone = document.getElementById('phone');
        const birthDate = document.getElementById('birth_date');
        const gender = document.getElementById('gender');
        const city = document.getElementById('city');
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirmation');

        // Name validation
        if (!name.value.trim()) {
            showError('name', 'الاسم مطلوب');
            isValid = false;
        } else if (name.value.trim().length < 3) {
            showError('name', 'الاسم يجب أن يكون 3 أحرف على الأقل');
            isValid = false;
        } else {
            clearError('name');
            formData.fullName = name.value;
        }

        // Email validation
        if (!email.value.trim()) {
            showError('email', 'البريد الإلكتروني مطلوب');
            isValid = false;
        } else if (!validateEmail(email.value)) {
            showError('email', 'البريد الإلكتروني غير صحيح');
            isValid = false;
        } else {
            clearError('email');
            formData.email = email.value;
        }

        // National ID validation
        if (!nationalId.value.trim()) {
            showError('national_id', 'رقم الهوية مطلوب');
            isValid = false;
        } else if (!validateNationalId(nationalId.value)) {
            showError('national_id', 'رقم الهوية يجب أن يكون 9 أرقام');
            isValid = false;
        } else {
            clearError('national_id');
            formData.nationalId = nationalId.value;
        }

        // Phone validation
        if (!phone.value.trim()) {
            showError('phone', 'رقم الجوال مطلوب');
            isValid = false;
        } else if (!validatePhone(phone.value)) {
            showError('phone', 'رقم الجوال غير صحيح');
            isValid = false;
        } else {
            clearError('phone');
            formData.phone = phone.value;
        }

        // Birth date validation
        if (!birthDate.value) {
            showError('birth_date', 'تاريخ الميلاد مطلوب');
            isValid = false;
        } else {
            const age = validateAge(birthDate.value);
            if (age < 18) {
                showError('birth_date', 'يجب أن يكون عمرك 18 سنة على الأقل');
                isValid = false;
            } else if (age > 65) {
                showError('birth_date', 'العمر المسموح هو من 18 إلى 65 سنة');
                isValid = false;
            } else {
                clearError('birth_date');
                formData.birthDate = birthDate.value;
            }
        }

        // Gender validation
        if (!gender.value) {
            showError('gender', 'اختر الجنس');
            isValid = false;
        } else {
            clearError('gender');
            formData.gender = gender.value;
        }

        // City validation
        if (!city.value.trim()) {
            showError('city', 'المدينة مطلوبة');
            isValid = false;
        } else {
            clearError('city');
            formData.city = city.value;
        }

        // Password validation
        if (!password.value) {
            showError('password', 'كلمة السر مطلوبة');
            isValid = false;
        } else if (!validatePassword(password.value)) {
            showError('password', 'كلمة السر يجب أن تكون 8 أحرف على الأقل');
            isValid = false;
        } else {
            clearError('password');
        }

        // Password confirmation validation
        if (!passwordConfirm.value) {
            showError('password_confirmation', 'تأكيد كلمة السر مطلوب');
            isValid = false;
        } else if (password.value !== passwordConfirm.value) {
            showError('password_confirmation', 'كلمات السر غير متطابقة');
            isValid = false;
        } else {
            clearError('password_confirmation');
        }
    } else if (step === 2) {
        // Validate Terms Checkbox
        const termsCheckbox = document.getElementById('termsCheckbox');
        if (!termsCheckbox.checked) {
            showError('termsCheckbox', 'يجب الموافقة على الشروط');
            isValid = false;
        } else {
            clearError('termsCheckbox');
        }
    }

    return isValid;
}

// Update progress steps
function updateProgressSteps() {
    const steps = document.querySelectorAll('.step');

    steps.forEach((step, index) => {
        const stepNum = index + 1;
        if (stepNum < currentStep) {
            step.classList.add('completed');
            step.classList.remove('active');
        } else if (stepNum === currentStep) {
            step.classList.add('active');
            step.classList.remove('completed');
        } else {
            step.classList.remove('active', 'completed');
        }
    });
}

// Show step
function showStep(step) {
    const formSteps = document.querySelectorAll('.form-step');

    formSteps.forEach((formStep, index) => {
        if (index + 1 === step) {
            formStep.classList.add('active');
        } else {
            formStep.classList.remove('active');
        }
    });

    // Update buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    if (step === 1) {
        prevBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'flex';
    }

    if (step === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'flex';
    } else {
        nextBtn.style.display = 'flex';
        submitBtn.style.display = 'none';
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Populate review section
function populateReview() {
    const personalInfoReview = document.getElementById('personalInfoReview');

    if (personalInfoReview) {
        personalInfoReview.innerHTML = `
            <div class="review-item">
                <span class="review-label">الاسم</span>
                <span class="review-value">${formData.fullName || '-'}</span>
            </div>
            <div class="review-item">
                <span class="review-label">البريد الإلكتروني</span>
                <span class="review-value">${formData.email || '-'}</span>
            </div>
            <div class="review-item">
                <span class="review-label">رقم الهوية</span>
                <span class="review-value">${formData.nationalId || '-'}</span>
            </div>
            <div class="review-item">
                <span class="review-label">رقم الجوال</span>
                <span class="review-value">${formData.phone || '-'}</span>
            </div>
            <div class="review-item">
                <span class="review-label">تاريخ الميلاد</span>
                <span class="review-value">${formData.birthDate || '-'}</span>
            </div>
            <div class="review-item">
                <span class="review-label">الجنس</span>
                <span class="review-value">${formData.gender === 'male' ? 'ذكر' : 'أنثى'}</span>
            </div>
            <div class="review-item" style="grid-column: 1 / -1;">
                <span class="review-label">المدينة / العنوان</span>
                <span class="review-value">${formData.city || '-'}</span>
            </div>
        `;
    }
}

// Navigation handlers
function initNavigation() {
    const form = document.getElementById('donorRegistrationForm');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (validateStep(currentStep)) {
                currentStep++;
                updateProgressSteps();
                showStep(currentStep);
                if (currentStep === 2) {
                    populateReview();
                }
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentStep > 1) {
                currentStep--;
                updateProgressSteps();
                showStep(currentStep);
            }
        });
    }

    if (submitBtn && form) {
        submitBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            
            if (!validateStep(currentStep)) {
                return;
            }

            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            try {
                // The form will be submitted normally through the Laravel route
                form.submit();
            } catch (error) {
                console.error('Error:', error);
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });
    }

    // Also handle form submission for direct submit
    if (form) {
        form.addEventListener('submit', (e) => {
            // Only stop default if we're on step 1 or 2 (multi-step)
            if (currentStep < totalSteps) {
                e.preventDefault();
            }
        });
    }
}

// Real-time validation
function initRealTimeValidation() {
    const form = document.getElementById('donorRegistrationForm');
    if (!form) return;

    const fields = form.querySelectorAll('input, select');

    fields.forEach(field => {
        field.addEventListener('blur', () => {
            if (field.value.trim()) {
                clearError(field.id);
            }
        });

        field.addEventListener('change', () => {
            if (field.value.trim()) {
                clearError(field.id);
            }
        });
    });
}

// Initialize all functions
document.addEventListener('DOMContentLoaded', () => {
    showStep(currentStep);
    updateProgressSteps();
    initNavigation();
    initRealTimeValidation();
});
