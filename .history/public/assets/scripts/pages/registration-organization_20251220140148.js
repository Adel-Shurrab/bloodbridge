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
                    showError("licenseUpload", "حجم الملف يجب أن يكون أقل من 5 ميغا بايت");
                    fileInput.value = "";
                    return;
                }

                // Check file type
                const allowedTypes = ["application/pdf", "image/jpeg", "image/jpg", "image/png"];
                if (!allowedTypes.includes(file.type)) {
                    showError("licenseUpload", "نوع الملف غير مدعوم. يرجى اختيار PDF أو صورة");
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

    const errorElement = field.parentElement.querySelector(".error-message");

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

    const errorElement = field.parentElement.querySelector(".error-message");

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
        const organizationDescription = document.getElementById("organizationDescription").value.trim();

        clearError("organizationName");

        if (!organizationName) {
            showError("organizationName", "اسم المنظمة مطلوب");
            isValid = false;
        }

        if (!organizationType) {
            showError("organizationType", "نوع المنظمة مطلوب");
            isValid = false;
        }

        if (isValid) {
            formData.organizationName = organizationName;
            // formData.organizationType = organizationType; // Removed
            formData.organizationDescription = organizationDescription;
        }
    } else if (step === 2) {
        // Contact Information
        const contactEmail = document.getElementById("contactEmail").value.trim();
        const contactPhone = document.getElementById("contactPhone").value.trim();
        const streetAddress = document.getElementById("streetAddress").value.trim();
        const city = document.getElementById("city").value.trim();
        const state = document.getElementById("state").value.trim();
        const postalCode = document.getElementById("postalCode").value.trim();

        // Clear all errors first
        ["contactEmail", "contactPhone", "streetAddress", "city", "state"].forEach(clearError);

        if (!contactEmail) {
            showError("contactEmail", "البريد الإلكتروني مطلوب");
            isValid = false;
        } else if (!validateEmail(contactEmail)) {
            showError("contactEmail", "البريد الإلكتروني غير صحيح");
            isValid = false;
        }

        if (!contactPhone) {
            showError("contactPhone", "رقم الجوال مطلوب");
            isValid = false;
        } else if (!validatePhone(contactPhone)) {
            showError("contactPhone", "رقم الجوال غير صحيح");
            isValid = false;
        }

        if (!streetAddress) {
            showError("streetAddress", "اسم الشارع مطلوب");
            isValid = false;
        }

        if (!city) {
            showError("city", "اسم المدينة مطلوب");
            isValid = false;
        }

        if (!state) {
            showError("state", "المحافظة/الولاية مطلوبة");
            isValid = false;
        }

        if (isValid) {
            formData.contactEmail = contactEmail;
            formData.contactPhone = contactPhone;
            formData.streetAddress = streetAddress;
            formData.city = city;
            formData.state = state;
            formData.postalCode = postalCode;
        }
    } else if (step === 3) {
        // Documentation & Administration
        const licenseNumber = document.getElementById("licenseNumber").value.trim();
        const licenseUpload = document.getElementById("licenseUpload");
        const adminName = document.getElementById("adminName").value.trim();
        const adminEmail = document.getElementById("adminEmail").value.trim();
        const adminPassword = document.getElementById("adminPassword").value;
        const adminPassword_confirmation = document.getElementById("adminPassword_confirmation").value;

        // Clear all errors first
        ["licenseNumber", "licenseUpload", "adminName", "adminEmail", "adminPassword", "adminPassword_confirmation"].forEach(clearError);

        if (!licenseNumber) {
            showError("licenseNumber", "رقم الترخيص مطلوب");
            isValid = false;
        }

        if (!licenseUpload.files.length) {
            showError("licenseUpload", "يرجى تحميل الرخصة");
            isValid = false;
        }

        if (!adminName) {
            showError("adminName", "اسم جهة الاتصال مطلوب");
            isValid = false;
        }

        if (!adminEmail) {
            showError("adminEmail", "البريد الإلكتروني مطلوب");
            isValid = false;
        } else if (!validateEmail(adminEmail)) {
            showError("adminEmail", "البريد الإلكتروني غير صحيح");
            isValid = false;
        }

        if (!adminPassword) {
            showError("adminPassword", "كلمة السر مطلوبة");
            isValid = false;
        } else if (!validatePassword(adminPassword)) {
            showError("adminPassword", "كلمة السر يجب أن تكون 8 أحرف على الأقل");
            isValid = false;
        }

        if (!adminPassword_confirmation) {
            showError("adminPassword_confirmation", "تأكيد كلمة السر مطلوب");
            isValid = false;
        } else if (adminPassword !== adminPassword_confirmation) {
            showError("adminPassword_confirmation", "كلمة السر غير متطابقة");
            isValid = false;
        }

        if (isValid) {
            formData.licenseNumber = licenseNumber;
            formData.adminName = adminName;
            formData.adminEmail = adminEmail;
            formData.adminPassword = adminPassword;
        }
    } else if (step === 4) {
        // Review & Confirm
        const termsAgree = document.getElementById("termsAgree");
        const errorElement = termsAgree.parentElement.parentElement.querySelector(".error-message");

        if (!termsAgree.checked) {
            if (errorElement) {
                errorElement.textContent = "يجب الموافقة على الشروط والأحكام";
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

// Get organization type label
function getOrganizationTypeLabel(type) {
    const types = {
        hospital: "مستشفى",
        government_clinic: "عيادة حكومية",
        private_clinic: "عيادة خاصة",
        blood_bank: "بنك دم",
        ngo: "منظمة غير حكومية",
    };
    return types[type] || type;
}

// Populate review section
function populateReview() {
    const organizationInfoReview = document.getElementById("organizationInfoReview");
    const contactInfoReview = document.getElementById("contactInfoReview");
    const adminInfoReview = document.getElementById("adminInfoReview");

    // Organization Information
    organizationInfoReview.innerHTML = `
        <div class="review-item">
            <span class="review-label">اسم المنظمة</span>
            <span class="review-value">${formData.organizationName}</span>
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
            <span class="review-value">${formData.streetAddress}, ${formData.city}, ${formData.state}${formData.postalCode ? ", " + formData.postalCode : ""}</span>
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
            <span class="review-value">${formData.licenseFile ? formData.licenseFile.name : "غير محدد"}</span>
        </div>
        <div class="review-item">
            <span class="review-label">اسم المسؤول</span>
            <span class="review-value">${formData.adminName}</span>
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

// Initialize all functions
document.addEventListener("DOMContentLoaded", () => {
    initNavbarScroll();
    initMobileMenu();
    initPasswordToggle();
    initFileUpload();
    initNavigation();
    showStep(currentStep);
    updateProgressSteps();

    // Check if coming from registration intro
    const userType = sessionStorage.getItem("userType");
    if (userType !== "organization") {
        // Redirect back if not organization
        console.log("User type mismatch");
    }
});