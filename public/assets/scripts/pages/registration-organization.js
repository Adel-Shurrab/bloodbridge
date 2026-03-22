// Registration Organization Page JavaScript

let currentStep = 1;
const totalSteps = 4;
const formData = {};

// Navbar scroll effect
function initNavbarScroll() {
    window.addEventListener("scroll", () => {
        const navbar = document.getElementById("navbar");
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });
}

// Mobile Menu functionality
function initMobileMenu() {
    const mobileMenuBtn = document.getElementById("mobile-menu-btn");
    const mobileMenuCloseBtn = document.getElementById("mobile-menu-close");
    const mobileNav = document.getElementById("mobile-nav");
    const overlay = document.getElementById("overlay");
    const mobileNavLinks = document.querySelectorAll(".mobile-nav-links a");

    const openMenu = () => {
        mobileNav.classList.add("open");
        overlay.classList.add("active");
        document.body.style.overflow = "hidden";
    };

    const closeMenu = () => {
        mobileNav.classList.remove("open");
        overlay.classList.remove("active");
        document.body.style.overflow = "";
    };

    if (mobileMenuBtn && mobileNav && mobileMenuCloseBtn && overlay) {
        mobileMenuBtn.addEventListener("click", openMenu);
        mobileMenuCloseBtn.addEventListener("click", closeMenu);
        overlay.addEventListener("click", closeMenu);

        mobileNavLinks.forEach((link) => {
            link.addEventListener("click", closeMenu);
        });
    }
}

// Password toggle functionality
function initPasswordToggle() {
    const toggleButtons = document.querySelectorAll(".toggle-password");

    toggleButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const targetId = button.getAttribute("data-target");
            const passwordInput = document.getElementById(targetId);
            const eyeIcon = button.querySelector(".eye-icon");

            const type =
                passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            eyeIcon.textContent = type === "password" ? "👁️" : "🙈";
        });
    });
}

// File upload functionality
function initFileUpload() {
    const fileInput = document.getElementById("licenseUpload");
    const fileUploadDisplay = document.getElementById("fileUploadDisplay");
    const fileSelected = document.getElementById("fileSelected");
    const fileName = document.getElementById("fileName");
    const fileRemove = document.getElementById("fileRemove");

    if (fileInput && fileUploadDisplay) {
        fileUploadDisplay.addEventListener("click", () => {
            fileInput.click();
        });

        fileInput.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (file) {
                // Check file size (5MB max)
                const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                if (file.size > maxSize) {
                    showError("licenseUpload", __("File too large"));
                    fileInput.value = "";
                    return;
                }

                // Check file type
                const allowedTypes = ["application/pdf", "image/jpeg", "image/jpg", "image/png"];
                if (!allowedTypes.includes(file.type)) {
                    showError("licenseUpload", __("File type not supported"));
                    fileInput.value = "";
                    return;
                }

                clearError("licenseUpload");
                fileName.textContent = file.name;
                fileUploadDisplay.style.display = "none";
                fileSelected.style.display = "flex";
                formData.licenseFile = file;
            }
        });

        if (fileRemove) {
            fileRemove.addEventListener("click", (e) => {
                e.stopPropagation();
                fileInput.value = "";
                fileUploadDisplay.style.display = "flex";
                fileSelected.style.display = "none";
                delete formData.licenseFile;
            });
        }
    }
}

// 24/7 Toggle functionality
function initOpen247() {
    const isOpen247 = document.getElementById("isOpen247");
    const operatingHoursContainer = document.getElementById("operatingHoursContainer");
    const openingTimeInput = document.getElementById("opening_time");
    const closingTimeInput = document.getElementById("closing_time");

    if (isOpen247 && operatingHoursContainer) {
        isOpen247.addEventListener("change", function () {
            if (this.checked) {
                operatingHoursContainer.style.display = "none";
                openingTimeInput.value = "";
                closingTimeInput.value = "";
            } else {
                operatingHoursContainer.style.display = "block";
            }
        });

        // Initialize state
        if (isOpen247.checked) {
            operatingHoursContainer.style.display = "none";
        }
    }
}

// Form validation functions
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^(\+970|0)?[5-9][0-9]{8}$/;
    return phoneRegex.test(phone.replace(/\s/g, ""));
}

function validatePassword(password) {
    return password.length >= 8;
}

// Show error message
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const parent = field.closest(".form-group");
    const errorElement = parent ? parent.querySelector(".error-message") : null;

    field.classList.add("error");
    if (errorElement) {
        errorElement.textContent = message;
    }

    // Shake animation
    field.style.animation = "shake 0.5s";
    setTimeout(() => {
        field.style.animation = "";
    }, 500);
}

// Clear error message
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const parent = field.closest(".form-group");
    const errorElement = parent ? parent.querySelector(".error-message") : null;

    field.classList.remove("error");
    if (errorElement) {
        errorElement.textContent = "";
    }
}

// Add shake animation
const shakeStyle = document.createElement("style");
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
        // Organization Information
        const organizationName = document.getElementById("organizationName").value.trim();
        const isOpen247Val = document.getElementById("isOpen247").checked;
        const openingTime = document.getElementById("opening_time").value;
        const closingTime = document.getElementById("closing_time").value;
        const dailyCapacity = document.getElementById("daily_capacity").value;
        const workingDays = Array.from(document.querySelectorAll('input[name="working_days[]"]:checked')).map(cb => cb.value);
        const organizationDescription = document.getElementById("organizationDescription").value.trim();

        ["organizationName", "opening_time", "closing_time", "daily_capacity", "working_days"].forEach(clearError);

        if (!organizationName) {
            showError("organizationName", __("Org name required"));
            isValid = false;
        }

        if (!isOpen247Val) {
            if (!openingTime) {
                showError("opening_time", __("Opening time required"));
                isValid = false;
            }
            if (!closingTime) {
                showError("closing_time", __("Closing time required"));
                isValid = false;
            }
        }

        if (workingDays.length === 0) {
            const workingDaysContainer = document.querySelector('.checkbox-grid').closest('.form-group');
            if (workingDaysContainer) {
                const errorSpan = workingDaysContainer.querySelector('.error-message');
                if (errorSpan) {
                    errorSpan.textContent = __("Min one working day");
                    workingDaysContainer.classList.add('error');
                }
            }
            isValid = false;
        }

        if (!dailyCapacity || parseInt(dailyCapacity) <= 0) {
            showError("daily_capacity", __("Daily capacity required"));
            isValid = false;
        }

        if (isValid) {
            formData.organizationName = organizationName;
            formData.isOpen247 = isOpen247Val;
            formData.openingTime = isOpen247Val ? null : openingTime;
            formData.closingTime = isOpen247Val ? null : closingTime;
            formData.dailyCapacity = dailyCapacity;
            formData.workingDays = workingDays;
            formData.organizationDescription = organizationDescription;
        }
    } else if (step === 2) {
        // Contact Information
        const contactEmail = document.getElementById("contactEmail").value.trim();
        const contactPhone = document.getElementById("contactPhone").value.trim();

        const governorateSelect = document.getElementById("governorate_id");
        const governorateId = governorateSelect.value;
        const governorateName = governorateSelect.options[governorateSelect.selectedIndex]?.text || '';

        // Clear all errors first
        ["contactEmail", "contactPhone", "governorate_id"].forEach(clearError);

        if (!contactEmail) {
            showError("contactEmail", __("Email is required"));
            isValid = false;
        } else if (!validateEmail(contactEmail)) {
            showError("contactEmail", __("Invalid email address"));
            isValid = false;
        }

        if (!contactPhone) {
            showError("contactPhone", __("Mobile number is required"));
            isValid = false;
        } else if (!validatePhone(contactPhone)) {
            showError("contactPhone", __("Invalid mobile number"));
            isValid = false;
        }



        if (!governorateId) {
            showError("governorate_id", __("Governorate is required"));
            isValid = false;
        }

        if (isValid) {
            formData.contactEmail = contactEmail;
            formData.contactPhone = contactPhone;

            formData.governorate_id = governorateId;
            formData.governorateName = governorateName;
        }
    } else if (step === 3) {
        // Documentation & Administration
        const licenseNumber = document.getElementById("licenseNumber").value.trim();
        const licenseUpload = document.getElementById("licenseUpload");
        const adminName = document.getElementById("adminName").value.trim();
        const responsiblePersonPosition = document.getElementById("responsible_person_position").value.trim();
        const adminEmail = document.getElementById("adminEmail").value.trim();
        const adminPassword = document.getElementById("adminPassword").value;
        const adminPassword_confirmation = document.getElementById("adminPassword_confirmation").value;

        // Clear all errors first
        ["licenseNumber", "licenseUpload", "adminName", "responsible_person_position", "adminEmail", "adminPassword", "adminPassword_confirmation"].forEach(clearError);

        if (!licenseNumber) {
            showError("licenseNumber", __("License number required"));
            isValid = false;
        }

        if (!licenseUpload.files.length) {
            showError("licenseUpload", __("Please upload license"));
            isValid = false;
        }

        if (!adminName) {
            showError("adminName", __("Contact name required"));
            isValid = false;
        }

        if (!responsiblePersonPosition) {
            showError("responsible_person_position", __("Job title required"));
            isValid = false;
        }

        if (!adminEmail) {
            showError("adminEmail", __("Email is required"));
            isValid = false;
        } else if (!validateEmail(adminEmail)) {
            showError("adminEmail", __("Invalid email address"));
            isValid = false;
        }

        if (!adminPassword) {
            showError("adminPassword", __("Password is required"));
            isValid = false;
        } else if (!validatePassword(adminPassword)) {
            showError("adminPassword", __("Password must be at least 8 characters"));
            isValid = false;
        }

        if (!adminPassword_confirmation) {
            showError("adminPassword_confirmation", __("Confirm password is required"));
            isValid = false;
        } else if (adminPassword !== adminPassword_confirmation) {
            showError("adminPassword_confirmation", __("Passwords do not match"));
            isValid = false;
        }

        if (isValid) {
            formData.licenseNumber = licenseNumber;
            formData.adminName = adminName;
            formData.responsiblePersonPosition = responsiblePersonPosition;
            formData.adminEmail = adminEmail;
            formData.adminPassword = adminPassword;
        }
    } else if (step === 4) {
        // Review & Confirm
        const termsAgree = document.getElementById("termsAgree");
        const errorElement = termsAgree.parentElement.parentElement.querySelector(".error-message");

        if (!termsAgree.checked) {
            if (errorElement) {
                errorElement.textContent = __("Must agree to terms");
            }
            isValid = false;
        } else {
            if (errorElement) {
                errorElement.textContent = "";
            }
        }
    }

    return isValid;
}

// Update progress steps
function updateProgressSteps() {
    const steps = document.querySelectorAll(".step");

    steps.forEach((step, index) => {
        const stepNumber = index + 1;

        if (stepNumber < currentStep) {
            step.classList.add("completed");
            step.classList.remove("active");
        } else if (stepNumber === currentStep) {
            step.classList.add("active");
            step.classList.remove("completed");
        } else {
            step.classList.remove("active", "completed");
        }
    });
}

// Show step
function showStep(step) {
    const formSteps = document.querySelectorAll(".form-step");

    formSteps.forEach((formStep, index) => {
        if (index + 1 === step) {
            formStep.classList.add("active");
        } else {
            formStep.classList.remove("active");
        }
    });

    // Update buttons
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const submitBtn = document.getElementById("submitBtn");

    if (step === 1) {
        prevBtn.style.display = "none";
    } else {
        prevBtn.style.display = "flex";
    }

    if (step === totalSteps) {
        nextBtn.style.display = "none";
        submitBtn.style.display = "flex";

        // Populate review section
        populateReview();
    } else {
        nextBtn.style.display = "flex";
        submitBtn.style.display = "none";
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: "smooth" });
}

// Populate review section
function populateReview() {
    const organizationInfoReview = document.getElementById("organizationInfoReview");
    const contactInfoReview = document.getElementById("contactInfoReview");
    const adminInfoReview = document.getElementById("adminInfoReview");

    const getWorkingHoursText = () => {
        if (formData.isOpen247) return __("Working 24/7");
        if (formData.openingTime && formData.closingTime) return `${formData.openingTime} - ${formData.closingTime}`;
        return __("Not specified");
    };

    // Organization Information
    organizationInfoReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">اسم المنظمة</span>
            <span class="review-value">${formData.organizationName}</span>
        </div>
        <div class="review-item">
            <span class="review-label">مواعيد العمل</span>
            <span class="review-value">${getWorkingHoursText()}</span>
        </div>
        <div class="review-item">
            <span class="review-label">أيام العمل</span>
            <span class="review-value">${formData.workingDays && formData.workingDays.length > 0 ? formData.workingDays.join(", ") : __("Not specified")}</span>
        </div>
        <div class="review-item">
            <span class="review-label">القدرة الاستيعابية</span>
            <span class="review-value">${formData.dailyCapacity || 0} متبرع يومياً</span>
        </div>
        ${formData.organizationDescription
            ? `
        <div class="review-item" style="grid-column: 1 / -1;">
            <span class="review-label">وصف المنظمة</span>
            <span class="review-value">${formData.organizationDescription}</span>
        </div>
        `
            : ""
        }
    `;

    // Contact Information
    contactInfoReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">البريد الإلكتروني</span>
            <span class="review-value">${formData.contactEmail}</span>
        </div>
        <div class="review-item">
            <span class="review-label">رقم الجوال</span>
            <span class="review-value">${formData.contactPhone}</span>
        </div>
        <div class="review-item" style="grid-column: 1 / -1;">
            <span class="review-label">العنوان</span>
            <span class="review-value">${formData.governorateName}</span>
        </div>
    `;

    // Admin Information
    adminInfoReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">رقم الترخيص</span>
            <span class="review-value">${formData.licenseNumber}</span>
        </div>
        <div class="review-item">
            <span class="review-label">الرخصة المحملة</span>
            <span class="review-value">${formData.licenseFile ? formData.licenseFile.name : __("Not specified")}</span>
        </div>
        <div class="review-item">
            <span class="review-label">اسم المسؤول</span>
            <span class="review-value">${formData.adminName}</span>
        </div>
        <div class="review-item">
            <span class="review-label">المسمى الوظيفي</span>
            <span class="review-value">${formData.responsiblePersonPosition}</span>
        </div>
        <div class="review-item">
            <span class="review-label">البريد الإلكتروني للمسؤول</span>
            <span class="review-value">${formData.adminEmail}</span>
        </div>
    `;
}

// Navigation handlers
function initNavigation() {
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const submitBtn = document.getElementById("submitBtn");

    nextBtn.addEventListener("click", () => {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
            updateProgressSteps();
        }
    });

    prevBtn.addEventListener("click", () => {
        currentStep--;
        showStep(currentStep);
        updateProgressSteps();
    });

    submitBtn.addEventListener("click", (e) => {
        if (!validateStep(currentStep)) {
            e.preventDefault();
            return;
        }

        // Show loading
        submitBtn.classList.add("loading");
        // We don't disable here because we want the form to actually submit
        // submitBtn.disabled = true; 
    });
}

// Simulate registration API call
function simulateRegistration() {
    return new Promise((resolve) => {
        setTimeout(() => {
            console.log("Registration data:", formData);
            resolve();
        }, 2000);
    });
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
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&accept-language=${window.currentLocale || 'ar'}`,
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

            let errorMessage = __('Failed to get location');

            if (error.code === 1) {
                errorMessage = __('Please allow access to your location');
            } else if (error.code === 2) {
                errorMessage = __('Location is unavailable');
            } else if (error.code === 3) {
                errorMessage = __('Request timeout');
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

// Initialize all functions
document.addEventListener("DOMContentLoaded", () => {
    initNavbarScroll();
    initMobileMenu();
    initPasswordToggle();
    initFileUpload();
    initNavigation();
    initOpen247();
    initGPSLocation();
    showStep(currentStep);
    updateProgressSteps();

    // Check if coming from registration intro
    const userType = sessionStorage.getItem("userType");
    if (userType !== "organization") {
        // Redirect back if not organization
        console.log("User type mismatch");
    }
});

