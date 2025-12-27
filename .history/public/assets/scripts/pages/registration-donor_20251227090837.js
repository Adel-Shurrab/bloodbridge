// Registration Donor Page JavaScript

let currentStep = 1;
const totalSteps = 3;
const formData = {};

// Eligibility check function
function checkEligibility() {
    const weight = parseInt(document.getElementById('weight').value) || 0;
    const height = parseInt(document.getElementById('height').value) || 0;
    const recentDonation = document.getElementById('recent_donation').value;
    const hasRecentSurgery = document.getElementById('has_recent_surgery').value;
    const lastDonationInput = document.getElementById('last_donation_date');
    const surgeryInput = document.getElementById('surgery_date');

    // Get raw date values
    const surgeryDateVal = document.getElementById('surgery_date').value;
    const lastDonationDateVal = document.getElementById('last_donation_date').value;

    const today = new Date();
    // Reset time part to ensure accurate day calculation
    today.setHours(0, 0, 0, 0);

    let isEligible = true;
    let nextEligibleDate = null;
    let ineligibilityReasons = [];

    // 1. Basic Checks
    if (weight < 50) {
        isEligible = false;
        ineligibilityReasons.push('الوزن أقل من الحد الأدنى (50 كغ)');
    }
    if (height < 140) {
        isEligible = false;
        ineligibilityReasons.push('الطول أقل من الحد الأدنى (140 سم)');
    }
    if (lastDonationInput.validity.badInput) {
        isEligible = false;
        ineligibilityReasons.push('تاريخ التبرع السابق غير مكتمل');
    }
    if (surgeryInput.validity.badInput) {
        isEligible = false;
        ineligibilityReasons.push('تاريخ العملية الجراحية غير مكتمل');
    }
    if (document.getElementById('chronic_disease').checked) {
        isEligible = false;
        ineligibilityReasons.push('وجود مرض مزمن');
    }
    if (document.getElementById('infection').checked) {
        isEligible = false;
        ineligibilityReasons.push('وجود عدوى حالية');
    }

    // 2. Donation Logic (90 Days)
    if (recentDonation === '1' && lastDonationDateVal) {
        const lastDonation = new Date(lastDonationDateVal);
        const diffTime = Math.abs(today - lastDonation);
        const daysSinceDonation = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        // Only block if less than 90 days
        if (daysSinceDonation < 90) {
            isEligible = false;
            ineligibilityReasons.push(`تبرعت قبل ${daysSinceDonation} يوم (يجب الانتظار 90 يوم)`);

            // Calculate Next Eligible Date
            const futureDate = new Date(lastDonation);
            futureDate.setDate(futureDate.getDate() + 90);

            // Logic: Take the latest date if multiple bans exist
            if (!nextEligibleDate || futureDate > nextEligibleDate) {
                nextEligibleDate = futureDate;
            }
        }
    }

    // 3. Surgery Logic (28 Days)
    if (hasRecentSurgery === '1' && surgeryDateVal) {
        const surgery = new Date(surgeryDateVal);
        const diffTime = Math.abs(today - surgery);
        const daysSinceSurgery = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (daysSinceSurgery < 28) {
            isEligible = false;
            ineligibilityReasons.push(`أجريت عملية قبل ${daysSinceSurgery} يوم (يجب الانتظار 28 يوم)`);

            const futureDate = new Date(surgery);
            futureDate.setDate(futureDate.getDate() + 28);

            if (!nextEligibleDate || futureDate > nextEligibleDate) {
                nextEligibleDate = futureDate;
            }
        }
    }

    // Return result
    return { isEligible, nextEligibleDate, ineligibilityReasons };
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

function initConditionalInputs() {
    const recentDonationSelect = document.getElementById('recent_donation');
    const lastDonationDateContainer = document.getElementById('last_donation_date_container');
    const lastDonationDateInput = document.getElementById('last_donation_date');
    const hasRecentSurgerySelect = document.getElementById('has_recent_surgery');
    const surgeryDateContainer = document.getElementById('surgery_date_container');
    const surgeryDateInput = document.getElementById('surgery_date');

    const updateVisibility = () => {
        try {
            if (recentDonationSelect && lastDonationDateContainer && lastDonationDateInput) {
                if (recentDonationSelect.value === '1') {
                    lastDonationDateContainer.style.display = 'block';
                    lastDonationDateInput.required = true;
                } else {
                    lastDonationDateContainer.style.display = 'none';
                    lastDonationDateInput.required = false;
                    lastDonationDateInput.value = '';
                }
            }

            if (hasRecentSurgerySelect && surgeryDateContainer && surgeryDateInput) {
                if (hasRecentSurgerySelect.value === '1') {
                    surgeryDateContainer.style.display = 'block';
                    surgeryDateInput.required = true;
                } else {
                    surgeryDateContainer.style.display = 'none';
                    surgeryDateInput.required = false;
                    surgeryDateInput.value = '';
                }
            }

            // Update eligibility status after visibility changes
            displayEligibilityStatus();
        } catch (e) {
            console.error('Error in updateVisibility:', e);
        }
    };

    if (recentDonationSelect) recentDonationSelect.addEventListener('change', updateVisibility);
    if (hasRecentSurgerySelect) hasRecentSurgerySelect.addEventListener('change', updateVisibility);

    // Initial state
    updateVisibility();
}

function initClearDateButtons() {
    const clearBtns = document.querySelectorAll('.clear-date-btn');

    clearBtns.forEach(btn => {
        const inputId = btn.getAttribute('data-target');
        const input = document.getElementById(inputId);

        const toggleBtn = () => {
            if (input.value) {
                btn.style.display = 'block';
            } else {
                btn.style.display = 'none';
            }
        };

        toggleBtn();
        input.addEventListener('input', toggleBtn);
        input.addEventListener('change', toggleBtn);

        btn.addEventListener('click', () => {
            input.value = '';
            toggleBtn();
            clearError(inputId);
            displayEligibilityStatus();
        });
    });
}

function initHealthProfileChangeListeners() {
    const healthFields = ['weight', 'height', 'chronic_disease', 'infection', 'surgery_date', 'last_donation_date', 'recent_donation', 'has_recent_surgery'];

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
        const bloodType = document.getElementById('blood_type').value;
        const recentDonation = document.getElementById('recent_donation').value;
        const hasRecentSurgery = document.getElementById('has_recent_surgery').value;
        const lastDonationInput = document.getElementById('last_donation_date');
        const surgeryInput = document.getElementById('surgery_date');

        // Clear all errors first
        ['weight', 'height', 'surgery_date', 'last_donation_date', 'recent_donation', 'has_recent_surgery'].forEach(clearError);

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

        // Check Recent Donation
        if (!recentDonation) {
            showError('recent_donation', 'يرجى الإجابة على هذا السؤال');
            isValid = false;
        } else if (recentDonation === '1' && !lastDonationInput.value) {
            showError('last_donation_date', 'تاريخ آخر تبرع مطلوب');
            isValid = false;
        } else if (recentDonation === '1' && lastDonationInput.validity.badInput) {
            showError('last_donation_date', 'يرجى إدخال التاريخ كاملاً');
            isValid = false;
        }

        // Check Recent Surgery
        if (!hasRecentSurgery) {
            showError('has_recent_surgery', 'يرجى الإجابة على هذا السؤال');
            isValid = false;
        } else if (hasRecentSurgery === '1' && !surgeryInput.value) {
            showError('surgery_date', 'تاريخ العملية الجراحية مطلوب');
            isValid = false;
        } else if (hasRecentSurgery === '1' && surgeryInput.validity.badInput) {
            showError('surgery_date', 'يرجى إدخال التاريخ كاملاً');
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
            formData.infection = document.getElementById('infection').checked;
            formData.recentDonationValue = recentDonation;
            formData.hasRecentSurgeryValue = hasRecentSurgery;
            formData.surgeryDate = document.getElementById('surgery_date').value;
            formData.lastDonationDate = document.getElementById('last_donation_date').value;
            formData.bloodType = bloodType;

            // Check eligibility and store result
            const eligibilityResult = checkEligibility();
            formData.isEligible = eligibilityResult.isEligible;
            formData.nextEligibleDate = eligibilityResult.nextEligibleDate;
            formData.ineligibilityReasons = eligibilityResult.ineligibilityReasons;
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
            <span class="review-label">فصيلة الدم</span>
            <span class="review-value">${formData.bloodType ? formData.bloodType : 'غير محدد'}</span>
        </div>
        <div class="review-item">
            <span class="review-label">مرض مزمن</span>
            <span class="review-value">${formData.chronicDisease ? 'نعم' : 'لا'}</span>
        </div>
        <div class="review-item">
            <span class="review-label">عدوى حالية</span>
            <span class="review-value">${formData.infection ? 'نعم' : 'لا'}</span>
        </div>
        <div class="review-item">
            <span class="review-label">تبرع سابقاً</span>
            <span class="review-value">${formData.recentDonationValue === '1' ? 'نعم' : 'لا'}</span>
        </div>
        ${formData.recentDonationValue === '1' ? `<div class="review-item">
            <span class="review-label">تاريخ آخر تبرع</span>
            <span class="review-value">${formData.lastDonationDate}</span>
        </div>` : ''}
        <div class="review-item">
            <span class="review-label">عملية جراحية سابقاً</span>
            <span class="review-value">${formData.hasRecentSurgeryValue === '1' ? 'نعم' : 'لا'}</span>
        </div>
        ${formData.hasRecentSurgeryValue === '1' ? `<div class="review-item">
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
        const parent = field.closest('.form-group');
        const errorElement = parent ? parent.querySelector('.error-message') : null;

        field.classList.add('error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        if (field.type !== 'checkbox') {
            field.style.animation = 'shake 0.5s';
            setTimeout(() => { field.style.animation = ''; }, 500);
        }
    }
}
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        const parent = field.closest('.form-group');
        const errorElement = parent ? parent.querySelector('.error-message') : null;

        field.classList.remove('error');
        if (errorElement) {
            errorElement.textContent = '';
        }
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

    // Update progress steps
    updateProgressSteps();

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
    // Call conditional inputs first to set up initial visibility
    initConditionalInputs();
    initNavigation();
    initPasswordToggle();
    initHealthProfileChangeListeners();
    initClearDateButtons();
    // Add shake style
    const style = document.createElement('style');
    style.textContent = `@keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }`;
    document.head.appendChild(style);
});