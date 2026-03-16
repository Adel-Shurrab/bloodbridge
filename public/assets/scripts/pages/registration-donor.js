// Registration Donor Page JavaScript - v1.2
console.log("Health Eligibility Logic v1.2 Loaded");

let currentStep = 1;
const totalSteps = 3;
const formData = {};

// Eligibility check function
function checkEligibility() {
    const weightField = document.getElementById('weight');
    const heightField = document.getElementById('height');
    const chronicDiseaseField = document.getElementById('chronic_disease');
    const infectionField = document.getElementById('infection');
    const recentDonationSelect = document.getElementById('recent_donation');
    const hasRecentSurgerySelect = document.getElementById('has_recent_surgery');
    const lastDonationInput = document.getElementById('last_donation_date');
    const surgeryInput = document.getElementById('surgery_date');

    // Use trim and handle empty strings carefully
    const weightRaw = weightField ? weightField.value.trim() : '';
    const heightRaw = heightField ? heightField.value.trim() : '';

    const recentDonation = recentDonationSelect ? recentDonationSelect.value : '';
    const hasRecentSurgery = hasRecentSurgerySelect ? hasRecentSurgerySelect.value : '';
    const surgeryDateVal = surgeryInput ? surgeryInput.value : '';
    const lastDonationDateVal = lastDonationInput ? lastDonationInput.value : '';

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let isEligible = true;
    let isPermanent = false;
    let nextEligibleDate = null;
    let ineligibilityReasons = [];

    if (weightRaw !== "" && !isNaN(weightRaw)) {
        const weight = parseFloat(weightRaw);
        if (weight > 0 && weight < window.donationRules.minWeight) {
            isEligible = false;
            ineligibilityReasons.push(__('Weight must be at least :weight kg', { weight: window.donationRules.minWeight }));
        }
    }

    if (heightRaw !== "" && !isNaN(heightRaw)) {
        const height = parseFloat(heightRaw);
        if (height > 0 && height < window.donationRules.minHeight) {
            isEligible = false;
            ineligibilityReasons.push(__('Height must be at least :height cm', { height: window.donationRules.minHeight }));
        }
    }

    // 3. Dates validity
    if (lastDonationInput && lastDonationInput.validity && lastDonationInput.validity.badInput) {
        isEligible = false;
        ineligibilityReasons.push(__('Please enter the full date'));
    }

    if (surgeryInput && surgeryInput.validity && surgeryInput.validity.badInput) {
        isEligible = false;
        ineligibilityReasons.push(__('Please enter the full date'));
    }

    // 4. Chronic Disease (Permanent)
    if (chronicDiseaseField && chronicDiseaseField.checked) {
        isEligible = false;
        isPermanent = true;
        ineligibilityReasons.push(__('Chronic Disease'));
    }

    // 5. Infection (Temporary)
    if (infectionField && infectionField.checked) {
        isEligible = false;
        ineligibilityReasons.push(__('Current Infection'));
    }

    // 6. Donation Logic (90 Days)
    if (recentDonation === '1' && lastDonationDateVal) {
        const lastDonation = new Date(lastDonationDateVal);
        if (!isNaN(lastDonation.getTime())) {
            const diffTime = Math.abs(today - lastDonation);
            const daysSinceDonation = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (daysSinceDonation < window.donationRules.minDaysBetweenDonations) {
                isEligible = false;
                ineligibilityReasons.push(__('Last donation date is required'));

                const futureDate = new Date(lastDonation);
                futureDate.setDate(futureDate.getDate() + window.donationRules.minDaysBetweenDonations);

                if (!nextEligibleDate || futureDate > nextEligibleDate) {
                    nextEligibleDate = futureDate;
                }
            }
        }
    }

    // 7. Surgery Logic (28 Days)
    if (hasRecentSurgery === '1' && surgeryDateVal) {
        const surgery = new Date(surgeryDateVal);
        if (!isNaN(surgery.getTime())) {
            const diffTime = Math.abs(today - surgery);
            const daysSinceSurgery = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (daysSinceSurgery < window.donationRules.minDaysAfterSurgery) {
                isEligible = false;
                ineligibilityReasons.push(__('Surgery date is required'));

                const futureDate = new Date(surgery);
                futureDate.setDate(futureDate.getDate() + window.donationRules.minDaysAfterSurgery);

                if (!nextEligibleDate || futureDate > nextEligibleDate) {
                    nextEligibleDate = futureDate;
                }
            }
        }
    }

    return { isEligible, nextEligibleDate, ineligibilityReasons, isPermanent };
}

// Display eligibility status
function displayEligibilityStatus() {
    const eligibilityBox = document.getElementById('eligibility-status-box');
    const title = document.getElementById('eligibility-status-title');
    const message = document.getElementById('eligibility-status-message');

    if (!eligibilityBox || !title || !message) return;

    const result = checkEligibility();

    if (result.isEligible) {
        eligibilityBox.style.display = 'none';
    } else {
        eligibilityBox.style.display = 'block';
        eligibilityBox.style.background = '#fef3c7';
        eligibilityBox.style.borderColor = '#f59e0b';

        title.textContent = result.isPermanent ? __('Permanently Ineligible') : __('Temporarily Ineligible');

        let messageText = `<strong>${__('Reasons:')}</strong><ul style="margin: 0.5rem 0 0 0; padding-right: 1.5rem;">`;
        result.ineligibilityReasons.forEach(reason => {
            messageText += `<li>${reason}</li>`;
        });

        if (result.nextEligibleDate) {
            const dateStr = result.nextEligibleDate.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            messageText += `</ul><p style="margin: 0.5rem 0 0 0;"><strong>${__('Eligible from: :date', { date: dateStr })}</strong></p>`;
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
        const governorateSelect = document.getElementById('governorate_id');
        const governorateId = governorateSelect.value;
        const governorateName = governorateSelect.options[governorateSelect.selectedIndex]?.text || '';
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        // Clear all errors first
        ['name', 'email', 'national_id', 'phone', 'birth_date', 'gender', 'governorate_id', 'password', 'password_confirmation'].forEach(clearError);

        if (!name) {
            showError('name', __('Name is required'));
            isValid = false;
        }

        if (!email) {
            showError('email', __('Email is required'));
            isValid = false;
        } else if (!validateEmail(email)) {
            showError('email', __('Invalid email address'));
            isValid = false;
        }

        if (!nationalId) {
            showError('national_id', __('National ID is required'));
            isValid = false;
        } else if (!validateNationalId(nationalId)) {
            showError('national_id', __('National ID must be 9 digits'));
            isValid = false;
        }

        if (!phone) {
            showError('phone', __('Mobile number is required'));
            isValid = false;
        } else if (!validatePhone(phone)) {
            showError('phone', __('Invalid mobile number'));
            isValid = false;
        }

        if (!birthDate) {
            showError('birth_date', __('Birth date is required'));
            isValid = false;
        } else {
            const age = validateAge(birthDate);
            if (age < window.donationRules.minAge) {
                showError('birth_date', __('Must be at least :age years old', { age: window.donationRules.minAge }));
                isValid = false;
            } else if (age > window.donationRules.maxAge) {
                showError('birth_date', __('Must not exceed :age years old', { age: window.donationRules.maxAge }));
                isValid = false;
            }
        }

        if (!gender) {
            showError('gender', __('Gender is required'));
            isValid = false;
        }

        if (!governorateId) {
            showError('governorate_id', __('Governorate is required'));
            isValid = false;
        }

        if (!password) {
            showError('password', __('Password is required'));
            isValid = false;
        } else if (!validatePassword(password)) {
            showError('password', __('Password must be at least 8 characters'));
            isValid = false;
        }

        if (!passwordConfirmation) {
            showError('password_confirmation', __('Confirm password is required'));
            isValid = false;
        } else if (password !== passwordConfirmation) {
            showError('password_confirmation', __('Passwords do not match'));
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
            formData.governorateId = governorateId;
            formData.governorateName = governorateName;
        }
    } else if (step === 2) {
        // Health Profile
        const weightField = document.getElementById('weight');
        const heightField = document.getElementById('height');
        const weight = weightField ? weightField.value.trim() : '';
        const height = heightField ? heightField.value.trim() : '';

        // Get Checkbox States
        const chronicDiseaseField = document.getElementById('chronic_disease');
        const chronicDisease = chronicDiseaseField ? chronicDiseaseField.checked : false;
        const bloodType = document.getElementById('blood_type').value;
        const recentDonation = document.getElementById('recent_donation').value;
        const hasRecentSurgery = document.getElementById('has_recent_surgery').value;
        const lastDonationInput = document.getElementById('last_donation_date');
        const surgeryInput = document.getElementById('surgery_date');

        // Clear all errors first
        ['weight', 'height', 'surgery_date', 'last_donation_date', 'recent_donation', 'has_recent_surgery'].forEach(clearError);

        // ... Weight and Height validation ...
        if (!weight) {
            showError('weight', __('Weight is required'));
            isValid = false;
        } else if (parseFloat(weight) < window.donationRules.minWeight) {
            showError('weight', __('Weight must be at least :weight kg', { weight: window.donationRules.minWeight }));
            isValid = false;
        }

        if (!height) {
            showError('height', __('Height is required'));
            isValid = false;
        } else if (parseFloat(height) < window.donationRules.minHeight) {
            showError('height', __('Height must be at least :height cm', { height: window.donationRules.minHeight }));
            isValid = false;
        }

        // Check Recent Donation
        if (!recentDonation) {
            showError('recent_donation', __('Please answer this question'));
            isValid = false;
        } else if (recentDonation === '1' && !lastDonationInput.value) {
            showError('last_donation_date', __('Last donation date is required'));
            isValid = false;
        } else if (recentDonation === '1' && lastDonationInput.validity.badInput) {
            showError('last_donation_date', __('Please enter the full date'));
            isValid = false;
        }

        // Check Recent Surgery
        if (!hasRecentSurgery) {
            showError('has_recent_surgery', __('Please answer this question'));
            isValid = false;
        } else if (hasRecentSurgery === '1' && !surgeryInput.value) {
            showError('surgery_date', __('Surgery date is required'));
            isValid = false;
        } else if (hasRecentSurgery === '1' && surgeryInput.validity.badInput) {
            showError('surgery_date', __('Please enter the full date'));
            isValid = false;
        }

        // CRITICAL LOGIC: Hard Stop for Chronic Disease
        if (isValid && chronicDisease) {
            // Update modal text to be more definitive
            const modal = document.getElementById('ineligibleModal');
            const modalBody = modal.querySelector('p');
            if (modalBody) {
                modalBody.innerHTML = `
                    نقدر رغبتك العالية في المساعدة وإنقاذ الأرواح.
                    <br><br>
                    بناءً على المعلومات الصحية المقدمة (وجود أمراض مزمنة)، وحرصاً منا على سلامتك الشخصية أولاً، لا يمكننا قبول
                    تسجيلك كمتبرع.
                    <br><br>
                    يمكنك دائماً المساهمة في نشر الوعي ودعم القضية بطرق أخرى.
                `;
            }
            // Show the "Thank You" Modal
            modal.classList.add('show');
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

            const bloodTypeSelect = document.getElementById('blood_type');
            formData.bloodType = bloodTypeSelect.value;
            formData.bloodTypeLabel = bloodTypeSelect.options[bloodTypeSelect.selectedIndex]?.text || 'غير محدد';

            // Check eligibility and store result
            const eligibilityResult = checkEligibility();
            formData.isEligible = eligibilityResult.isEligible;
            formData.isPermanent = eligibilityResult.isPermanent;
            formData.nextEligibleDate = eligibilityResult.nextEligibleDate;
            formData.ineligibilityReasons = eligibilityResult.ineligibilityReasons;
        }
    } else if (step === 3) {
        // Review & Confirm
        const termsAgree = document.getElementById('termsAgree');
        const errorElement = termsAgree.parentElement.parentElement.querySelector('.error-message');

        if (!termsAgree.checked) {
            if (errorElement) {
                errorElement.textContent = __('Must agree to terms');
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
            <span class="review-label">${__('Name')}</span>
            <span class="review-value">${formData.name}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Email')}</span>
            <span class="review-value">${formData.email}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('National ID')}</span>
            <span class="review-value">${formData.nationalId}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Phone')}</span>
            <span class="review-value">${formData.phone}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Birth Date')}</span>
            <span class="review-value">${formData.birthDate || __('Not specified')}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Gender')}</span>
            <span class="review-value">${formData.gender === 'male' ? __('Male') : __('Female')}</span>
        </div>
        <div class="review-item" style="grid-column: 1 / -1;">
            <span class="review-label">${__('Governorate')}</span>
            <span class="review-value">${formData.governorateName || __('Not specified')}</span>
        </div>
    `;

    // Health Information
    healthInfoReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">${__('Weight')}</span>
            <span class="review-value">${formData.weight} ${__('kg')}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Height')}</span>
            <span class="review-value">${formData.height} ${__('cm')}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Blood Type')}</span>
            <span class="review-value">${formData.bloodTypeLabel || __('Not specified')}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Chronic Disease')}</span>
            <span class="review-value">${formData.chronicDisease ? __('Yes') : __('No')}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Current Infection')}</span>
            <span class="review-value">${formData.infection ? __('Yes') : __('No')}</span>
        </div>
        <div class="review-item">
            <span class="review-label">${__('Previously Donated')}</span>
            <span class="review-value">${formData.recentDonationValue === '1' ? __('Yes') : __('No')}</span>
        </div>
        ${formData.recentDonationValue === '1' ? `<div class="review-item">
            <span class="review-label">${__('Last donation date')}</span>
            <span class="review-value">${formData.lastDonationDate || __('Not specified')}</span>
        </div>` : ''}
        <div class="review-item">
            <span class="review-label">${__('Previous Surgery')}</span>
            <span class="review-value">${formData.hasRecentSurgeryValue === '1' ? __('Yes') : __('No')}</span>
        </div>
        ${formData.hasRecentSurgeryValue === '1' ? `<div class="review-item">
            <span class="review-label">${__('Surgery date')}</span>
            <span class="review-value">${formData.surgeryDate || __('Not specified')}</span>
        </div>` : ''}
    `;

    // Eligibility Status
    if (!formData.isEligible) {
        eligibilityReviewBox.style.display = 'block';
        eligibilityReviewBox.style.background = '#fef3c7';
        eligibilityReviewBox.style.borderColor = '#f59e0b';
        eligibilityReviewTitle.textContent = formData.isPermanent ? __('Permanently Ineligible') : __('Temporarily Ineligible');

        let messageText = `<strong>${__('Reasons:')}</strong><ul style="margin: 0.5rem 0 0 0; padding-right: 1.5rem;">`;
        formData.ineligibilityReasons.forEach(reason => {
            messageText += `<li>${reason}</li>`;
        });

        if (formData.nextEligibleDate) {
            const dateStr = formData.nextEligibleDate.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            messageText += `</ul><p style="margin: 0.5rem 0 0 0;"><strong>${__('Eligible from: :date', { date: dateStr })}</strong></p>`;
        } else {
            messageText += '</ul>';
        }

        eligibilityReviewMessage.innerHTML = messageText;
    } else {
        eligibilityReviewBox.style.display = 'block';
        eligibilityReviewBox.style.background = '#d1fae5';
        eligibilityReviewBox.style.borderColor = '#10b981';
        eligibilityReviewIcon.textContent = '✓';
        eligibilityReviewTitle.textContent = __('Eligible to donate');
        eligibilityReviewMessage.innerHTML = `<p>${__('Eligibility Success Msg')}</p>`;
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

/**
 * Initialize GPS location button functionality
 */
function initGPSLocation() {
    const gpsBtn = document.getElementById('gps-location-btn');
    const locationInput = document.getElementById('auto_location_address');
    const latInput = document.getElementById('auto_lat');
    const lngInput = document.getElementById('auto_lng');

    if (!gpsBtn || !locationInput) return;

    let lastRequestTime = 0;
    const COOLDOWN_MS = 2000; // 2 seconds between requests

    gpsBtn.addEventListener('click', async function (e) {
        e.preventDefault();

        // Rate limiting check
        const now = Date.now();
        if (now - lastRequestTime < COOLDOWN_MS) {
            showError('auto_location_address', 'يرجى الانتظار قليلاً قبل المحاولة مرة أخرى');
            return;
        }
        lastRequestTime = now;

        // Show loading state
        const originalHTML = gpsBtn.innerHTML;
        gpsBtn.innerHTML = '<span style="animation: spin 1s linear infinite;">⌛</span>';
        gpsBtn.disabled = true;

        try {
            // Check if geolocation is supported
            if (!navigator.geolocation) {
                throw new Error('المتصفح لا يدعم تحديد الموقع الجغرافي');
            }

            // Get user's position
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            });

            const { latitude, longitude } = position.coords;

            // Store coordinates in hidden inputs if they exist
            if (latInput) latInput.value = latitude.toFixed(6);
            if (lngInput) lngInput.value = longitude.toFixed(6);

            // Reverse geocode to get address using Nominatim (OpenStreetMap)
            // Note: Nominatim requires a User-Agent header for identification
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&accept-language=ar`,
                {
                    headers: {
                        'User-Agent': 'BloodBridge/1.0' // Required by Nominatim usage policy
                    }
                }
            );

            if (!response.ok) {
                throw new Error('فشل في الحصول على العنوان');
            }

            const data = await response.json();

            // Extract address components
            const address = data.address || {};
            const addressParts = [
                address.road || address.neighbourhood,
                address.suburb || address.city_district,
                address.city || address.town || address.village,
                address.state
            ].filter(Boolean);

            const fullAddress = addressParts.join('، ') || data.display_name;

            // Update input with address
            locationInput.value = fullAddress;
            clearError('auto_location_address');

            // Show success message briefly
            gpsBtn.innerHTML = '<span style="color: #10b981;">✓</span>';
            setTimeout(() => {
                gpsBtn.innerHTML = originalHTML;
                gpsBtn.disabled = false;
            }, 2000);

        } catch (error) {
            console.error('GPS Error:', error);

            let errorMessage = 'فشل في تحديد الموقع';

            if (error.code === 1) {
                errorMessage = 'يرجى السماح بالوصول إلى موقعك';
            } else if (error.code === 2) {
                errorMessage = 'الموقع غير متاح حالياً';
            } else if (error.code === 3) {
                errorMessage = 'انتهت مهلة الطلب';
            }

            showError('auto_location_address', errorMessage);

            // Reset button
            gpsBtn.innerHTML = originalHTML;
            gpsBtn.disabled = false;
        }
    });
}

// Add spin animation for loading state
const spinStyle = document.createElement('style');
spinStyle.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(spinStyle);

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // Call conditional inputs first to set up initial visibility
    initConditionalInputs();
    initNavigation();
    initPasswordToggle();
    initHealthProfileChangeListeners();
    initClearDateButtons();
    initGPSLocation();
    // Add shake style
    const style = document.createElement('style');
    style.textContent = `@keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }`;
    document.head.appendChild(style);
});