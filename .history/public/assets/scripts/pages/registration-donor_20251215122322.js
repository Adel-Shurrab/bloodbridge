// Registration Donor Page JavaScript

let currentStep = 1;
const totalSteps = 3;
const formData = {};

// Eligibility check function
function checkEligibility() {
    const weight = parseInt(document.getElementById('weight').value) || 0;
    const height = parseInt(document.getElementById('height').value) || 0;
    const chronicDisease = document.getElementById('chronic_disease').checked;
    const recentDonation = document.getElementById('recent_donation').checked;
    const infection = document.getElementById('infection').checked;
    const isSmoker = document.getElementById('is_smoker').checked;
    const hasRecentSurgery = document.getElementById('has_recent_surgery').checked;
    const surgeryDate = document.getElementById('surgery_date').value;
    const lastDonationDate = document.getElementById('last_donation_date').value;

    const today = new Date();
    let isEligible = true;
    let isPermanentlyIneligible = false;
    let nextEligibleDate = null;
    let ineligibilityReasons = [];

    // Check chronic disease - PERMANENT INELIGIBILITY
    if (chronicDisease) {
        isEligible = false;
        isPermanentlyIneligible = true;
        ineligibilityReasons.push('وجود مرض مزمن');
    }

    // Check weight (minimum 50kg) - PERMANENT INELIGIBILITY
    if (weight < 50) {
        isEligible = false;
        isPermanentlyIneligible = true;
        ineligibilityReasons.push('الوزن أقل من الحد الأدنى (50 كغ)');
    }

    // Check height (minimum 140cm) - PERMANENT INELIGIBILITY
    if (height < 140) {
        isEligible = false;
        isPermanentlyIneligible = true;
        ineligibilityReasons.push('الطول أقل من الحد الأدنى (140 سم)');
    }

    // Check infection - TEMPORARY INELIGIBILITY (can check every 14 days)
    if (infection) {
        isEligible = false;
        const infectionDate = today.getTime();
        nextEligibleDate = new Date(infectionDate + (14 * 24 * 60 * 60 * 1000));
        ineligibilityReasons.push('وجود عدوى حالية');
    }

    // Check recent donation (90 days)
    if (lastDonationDate) {
        const lastDonation = new Date(lastDonationDate);
        const daysSinceDonation = Math.floor((today - lastDonation) / (1000 * 60 * 60 * 24));

        if (daysSinceDonation < 90) {
            isEligible = false;
            const daysRemaining = 90 - daysSinceDonation;
            ineligibilityReasons.push(`تبرعت قبل ${daysSinceDonation} أيام فقط (يجب أن تمضي 90 يوم)`);

            // Calculate next eligible date
            const futureDate = new Date(lastDonation);
            futureDate.setDate(futureDate.getDate() + 90);
            if (!nextEligibleDate || futureDate > nextEligibleDate) {
                nextEligibleDate = futureDate;
            }
        }
    }

    // Check recent surgery (28 days / 4 weeks)
    if (hasRecentSurgery && surgeryDate) {
        const surgery = new Date(surgeryDate);
        const daysSinceSurgery = Math.floor((today - surgery) / (1000 * 60 * 60 * 24));

        if (daysSinceSurgery < 28) {
            isEligible = false;
            const daysRemaining = 28 - daysSinceSurgery;
            ineligibilityReasons.push(`أجريت عملية جراحية قبل ${daysSinceSurgery} أيام فقط (يجب أن تمضي 4 أسابيع)`);

            // Update next eligible date if this is later
            const futureDate = new Date(surgery);
            futureDate.setDate(futureDate.getDate() + 28);
            if (!nextEligibleDate || futureDate > nextEligibleDate) {
                nextEligibleDate = futureDate;
            }
        }
    }

    // Store eligibility info in formData
    formData.isEligible = isEligible;
    formData.isPermanentlyIneligible = isPermanentlyIneligible;
    formData.nextEligibleDate = nextEligibleDate;
    formData.ineligibilityReasons = ineligibilityReasons;

    return { isEligible, isPermanentlyIneligible, nextEligibleDate, ineligibilityReasons };
}

// Display eligibility status
function displayEligibilityStatus() {
    const eligibilityBox = document.getElementById('eligibility-status-box');
    const title = document.getElementById('eligibility-status-title');
    const message = document.getElementById('eligibility-status-message');

    const { isEligible, nextEligibleDate, ineligibilityReasons } = checkEligibility();

    if (isEligible) {
        eligibilityBox.style.display = 'none';
    } else {
        eligibilityBox.style.display = 'block';
        eligibilityBox.style.background = '#fef3c7';
        eligibilityBox.style.borderColor = '#f59e0b';

        title.textContent = '⚠️ غير مؤهل مؤقتًا';

        let messageText = '<strong>الأسباب:</strong><ul style="margin: 0.5rem 0 0 0; padding-right: 1.5rem;">';
        ineligibilityReasons.forEach(reason => {
            messageText += `<li>${reason}</li>`;
        });

        if (nextEligibleDate) {
            const dateStr = nextEligibleDate.toLocaleDateString('ar-EG', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            messageText += `</ul><p style="margin: 0.5rem 0 0 0;"><strong>سيكون لديك الأهلية اعتباراً من: ${dateStr}</strong></p>`;
        } else {
            messageText += '</ul>';
        }

        message.innerHTML = messageText;
    }
}

// Add listener for health profile changes
function initHealthProfileChangeListeners() {
    const healthFields = ['weight', 'height', 'chronic_disease', 'recent_donation', 'infection', 'is_smoker', 'has_recent_surgery', 'surgery_date', 'last_donation_date'];

    healthFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', displayEligibilityStatus);
            field.addEventListener('input', displayEligibilityStatus);
        }
    });
}

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
        const city = document.getElementById('city').value.trim();
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        // Clear all errors first
        ['name', 'email', 'national_id', 'phone', 'birth_date', 'gender', 'city', 'password', 'password_confirmation'].forEach(clearError);

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
        // Health Profile
        const weight = document.getElementById('weight').value.trim();
        const height = document.getElementById('height').value.trim();
        // Get Checkbox States
        const chronicDisease = document.getElementById('chronic_disease').checked;

        // Clear all errors first
        ['weight', 'height', 'surgery_date', 'last_donation_date'].forEach(clearError);

        // ... Weight and Height validation ...
        if (!weight) {
            showError('weight', 'الوزن مطلوب');
            isValid = false;
        } else if (parseInt(weight) < 50) {
            showError('weight', 'الوزن يجب أن يكون 50 كغ على الأقل');
            isValid = false;
        }

        if (!height) {
            showError('height', 'الطول مطلوب');
            isValid = false;
        } else if (parseInt(height) < 140) {
            showError('height', 'الطول يجب أن يكون 140 سم على الأقل');
            isValid = false;
        }

        // CRITICAL LOGIC: Hard Stop for Chronic Disease
        if (isValid && chronicDisease) {
            // Show the "Thank You" Modal
            document.getElementById('ineligibleModal').classList.add('show');
            // Prevent going to next step
            return false;
        }

        if (isValid) {
            // Store health data for review step
            formData.weight = weight;
            formData.height = height;
            // ... rest of data storage
            formData.chronicDisease = chronicDisease;
            formData.recentDonation = document.getElementById('recent_donation').checked;
            formData.infection = document.getElementById('infection').checked;
            formData.isSmoker = document.getElementById('is_smoker').checked;
            formData.hasRecentSurgery = document.getElementById('has_recent_surgery').checked;
            formData.surgeryDate = document.getElementById('surgery_date').value;
            formData.lastDonationDate = document.getElementById('last_donation_date').value;

            // Check eligibility (Calculates Scenario 2)
            checkEligibility();
        }
    } else if (step === 3) {
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
    const healthInfoReview = document.getElementById('healthInfoReview');
    const eligibilityReviewBox = document.getElementById('eligibility-review-box');
    const eligibilityReviewIcon = document.getElementById('eligibility-review-icon');
    const eligibilityReviewTitle = document.getElementById('eligibility-review-title');
    const eligibilityReviewMessage = document.getElementById('eligibility-review-message');

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

    // Health Information
    healthInfoReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">الوزن</span>
            <span class="review-value">${formData.weight} كغ</span>
        </div>
        <div class="review-item">
            <span class="review-label">الطول</span>
            <span class="review-value">${formData.height} سم</span>
        </div>
        <div class="review-item">
            <span class="review-label">مرض مزمن</span>
            <span class="review-value">${formData.chronicDisease ? 'نعم' : 'لا'}</span>
        </div>
        <div class="review-item">
            <span class="review-label">مدخن</span>
            <span class="review-value">${formData.isSmoker ? 'نعم' : 'لا'}</span>
        </div>
        <div class="review-item">
            <span class="review-label">عدوى حالية</span>
            <span class="review-value">${formData.infection ? 'نعم' : 'لا'}</span>
        </div>
        <div class="review-item">
            <span class="review-label">تبرع مؤخراً</span>
            <span class="review-value">${formData.recentDonation ? 'نعم' : 'لا'}</span>
        </div>
        ${formData.lastDonationDate ? `<div class="review-item">
            <span class="review-label">تاريخ آخر تبرع</span>
            <span class="review-value">${formData.lastDonationDate}</span>
        </div>` : ''}
        <div class="review-item">
            <span class="review-label">عملية جراحية مؤخراً</span>
            <span class="review-value">${formData.hasRecentSurgery ? 'نعم' : 'لا'}</span>
        </div>
        ${formData.surgeryDate ? `<div class="review-item">
            <span class="review-label">تاريخ العملية الجراحية</span>
            <span class="review-value">${formData.surgeryDate}</span>
        </div>` : ''}
    `;

    // Eligibility Status
    if (!formData.isEligible) {
        eligibilityReviewBox.style.display = 'block';
        eligibilityReviewBox.style.background = '#fef3c7';
        eligibilityReviewBox.style.borderColor = '#f59e0b';
        eligibilityReviewIcon.textContent = '⚠️';
        eligibilityReviewTitle.textContent = 'غير مؤهل مؤقتًا';

        let messageText = '<strong>الأسباب:</strong><ul style="margin: 0.5rem 0 0 0; padding-right: 1.5rem;">';
        formData.ineligibilityReasons.forEach(reason => {
            messageText += `<li>${reason}</li>`;
        });

        if (formData.nextEligibleDate) {
            const dateStr = formData.nextEligibleDate.toLocaleDateString('ar-EG', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            messageText += `</ul><p style="margin: 0.5rem 0 0 0;"><strong>سيكون لديك الأهلية اعتباراً من: ${dateStr}</strong></p>`;
        } else {
            messageText += '</ul>';
        }

        eligibilityReviewMessage.innerHTML = messageText;
    } else {
        eligibilityReviewBox.style.display = 'block';
        eligibilityReviewBox.style.background = '#d1fae5';
        eligibilityReviewBox.style.borderColor = '#10b981';
        eligibilityReviewIcon.textContent = '✓';
        eligibilityReviewTitle.textContent = 'مؤهل للتبرع';
        eligibilityReviewMessage.innerHTML = '<p>تهانينا! أنت مؤهل للتبرع والمساهمة في إنقاذ الأرواح.</p>';
    }
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

// Helper functions
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
        // For checkboxes in labels, find the parent form-group
        let errorElement;
        if (field.type === 'checkbox') {
            let parent = field.closest('.form-group');
            if (parent) {
                errorElement = parent.querySelector('.error-message');
            }
        } else {
            errorElement = field.parentElement.querySelector('.error-message');
        }

        field.classList.add('error');
        if (errorElement) errorElement.textContent = message;
        if (field.type !== 'checkbox') {
            field.style.animation = 'shake 0.5s';
            setTimeout(() => { field.style.animation = ''; }, 500);
        }
    }
}
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        let errorElement;
        if (field.type === 'checkbox') {
            let parent = field.closest('.form-group');
            if (parent) {
                errorElement = parent.querySelector('.error-message');
            }
        } else {
            errorElement = field.parentElement.querySelector('.error-message');
        }

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
    initHealthProfileChangeListeners();
    // Add shake style
    const style = document.createElement('style');
    style.textContent = `@keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }`;
    document.head.appendChild(style);
});