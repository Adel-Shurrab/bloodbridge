// Registration Donor Page JavaScript

let currentStep = 1;
const totalSteps = 2; // Changed from 3 to 2
const formData = {};

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
        mobileNav.classList.add('open');
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

// Password toggle functionality
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll('.toggle-password');

    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const eyeIcon = button.querySelector('.eye-icon');

            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.textContent = type === 'password' ? '👁️' : '🙈';
        });
    });
}

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
    const errorElement = field.parentElement.querySelector('.error-message');

    field.classList.add('error');
    if (errorElement) {
        errorElement.textContent = message;
    }

    // Shake animation
    field.style.animation = 'shake 0.5s';
    setTimeout(() => {
        field.style.animation = '';
    }, 500);
}

// Clear error message
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorElement = field.parentElement.querySelector('.error-message');

    field.classList.remove('error');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

// Add shake animation
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
`;
document.head.appendChild(shakeStyle);

// Validate current step
function validateStep(step) {
    let isValid = true;

    if (step === 1) {
        // Personal Information
        const fullName = document.getElementById('fullName').value.trim();
        const email = document.getElementById('email').value.trim();
        const nationalId = document.getElementById('nationalId').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const birthDate = document.getElementById('birthDate').value;
        const gender = document.getElementById('gender').value;
        const address = document.getElementById('address').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Clear all errors first
        ['fullName', 'email', 'nationalId', 'phone', 'birthDate', 'gender', 'address', 'password', 'confirmPassword'].forEach(clearError);

        if (!fullName) {
            showError('fullName', 'الاسم مطلوب');
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
            showError('nationalId', 'رقم الهوية مطلوب');
            isValid = false;
        } else if (!validateNationalId(nationalId)) {
            showError('nationalId', 'رقم الهوية يجب أن يكون 9 أرقام');
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
            showError('birthDate', 'تاريخ الميلاد مطلوب');
            isValid = false;
        } else {
            const age = validateAge(birthDate);
            if (age < 18) {
                showError('birthDate', 'يجب أن يكون عمرك 18 سنة على الأقل');
                isValid = false;
            } else if (age > 65) {
                showError('birthDate', 'يجب أن يكون عمرك 65 سنة أو أقل');
                isValid = false;
            }
        }

        if (!gender) {
            showError('gender', 'الجنس مطلوب');
            isValid = false;
        }

        if (!address) {
            showError('address', 'العنوان مطلوب');
            isValid = false;
        }

        if (!password) {
            showError('password', 'كلمة السر مطلوبة');
            isValid = false;
        } else if (!validatePassword(password)) {
            showError('password', 'كلمة السر يجب أن تكون 8 أحرف على الأقل');
            isValid = false;
        }

        if (!confirmPassword) {
            showError('confirmPassword', 'تأكيد كلمة السر مطلوب');
            isValid = false;
        } else if (password !== confirmPassword) {
            showError('confirmPassword', 'كلمة السر غير متطابقة');
            isValid = false;
        }

        if (isValid) {
            // Store data
            formData.fullName = fullName;
            formData.email = email;
            formData.nationalId = nationalId;
            formData.phone = phone;
            formData.birthDate = birthDate;
            formData.gender = gender;
            formData.address = address;
            formData.password = password;
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

// Update progress steps
function updateProgressSteps() {
    const steps = document.querySelectorAll('.step');

    steps.forEach((step, index) => {
        const stepNumber = index + 1;

        if (stepNumber < currentStep) {
            step.classList.add('completed');
            step.classList.remove('active');
        } else if (stepNumber === currentStep) {
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

        // Populate review section
        populateReview();
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

    // Personal Information
    personalInfoReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">الاسم</span>
            <span class="review-value">${formData.fullName}</span>
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
            <span class="review-value">${formData.address}</span>
        </div>
    `;
}

// Navigation handlers
function initNavigation() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

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

    submitBtn.addEventListener('click', async (e) => {
        e.preventDefault();

        if (!validateStep(currentStep)) {
            return;
        }

        // Show loading
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        // Simulate API call
        try {
            await simulateRegistration();

            // Show success modal
            const successModal = document.getElementById('successModal');
            successModal.classList.add('show');

            // Reset form
            document.getElementById('donorRegistrationForm').reset();
            currentStep = 1;
            showStep(currentStep);
            updateProgressSteps();

        } catch (error) {
            alert('حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.');
        } finally {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }
    });
}

// Simulate registration API call
function simulateRegistration() {
    return new Promise((resolve) => {
        setTimeout(() => {
            console.log('Registration data:', formData);
            resolve();
        }, 2000);
    });
}

// Initialize all functions
document.addEventListener('DOMContentLoaded', () => {
    initNavbarScroll();
    initMobileMenu();
    initPasswordToggle();
    initNavigation();
    showStep(currentStep);
    updateProgressSteps();

    // Check if coming from registration intro
    const userType = sessionStorage.getItem('userType');
    if (userType !== 'donor') {
        // Redirect back if not donor
        console.log('User type mismatch');
    }
});