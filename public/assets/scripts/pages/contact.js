// Form validation
function validateForm(formData) {
    const errors = {};

    // Name validation
    if (!formData.name || formData.name.trim().length < 3) {
        errors.name = 'الاسم يجب أن يكون 3 أحرف على الأقل';
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!formData.email || !emailRegex.test(formData.email)) {
        errors.email = 'يرجى إدخال بريد إلكتروني صحيح';
    }

    // Subject validation
    if (!formData.subject || formData.subject.trim().length < 5) {
        errors.subject = 'الموضوع يجب أن يكون 5 أحرف على الأقل';
    }

    // Message validation
    if (!formData.message || formData.message.trim().length < 10) {
        errors.message = 'الرسالة يجب أن تكون 10 أحرف على الأقل';
    }

    // Privacy checkbox validation
    if (!formData.privacy) {
        errors.privacy = 'يجب الموافقة على سياسة الخصوصية';
    }

    return errors;
}

// Clear error messages
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => {
        if (!el.classList.contains('form-message')) {
            el.textContent = '';
        }
    });
    document.getElementById('errorMessage').style.display = 'none';
    document.getElementById('successMessage').style.display = 'none';
}

// Display errors
function displayErrors(errors) {
    clearErrors();
    Object.entries(errors).forEach(([field, message]) => {
        const errorEl = document.getElementById(field + 'Error');
        if (errorEl) {
            errorEl.textContent = message;
        }
    });
}

// Contact form submission
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();

        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value,
            privacy: document.getElementById('privacy').checked
        };

        // Validate form
        const errors = validateForm(formData);
        if (Object.keys(errors).length > 0) {
            Object.entries(errors).forEach(([field, message]) => {
                const errorEl = document.getElementById(field + 'Error');
                if (errorEl) {
                    errorEl.textContent = message;
                }
            });
            return;
        }

        // Submit form
        const submitBtn = document.getElementById('submitBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoader = submitBtn.querySelector('.btn-loader');

        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';

        try {
            const formDataObj = new FormData(contactForm);
            const response = await fetch(contactForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formDataObj
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 422 && result.errors) {
                    // Display validation errors from server
                    const serverErrors = {};
                    Object.entries(result.errors).forEach(([field, messages]) => {
                        serverErrors[field] = messages[0];
                    });
                    displayErrors(serverErrors);
                    throw new Error('Validation failed');
                }
                throw new Error(result.message || 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة لاحقاً.');
            }

            // Show success message
            document.getElementById('successMessage').textContent = result.message || 'تم إرسال رسالتك بنجاح! سنرد عليك قريباً.';
            document.getElementById('successMessage').style.display = 'block';

            // Reset form
            contactForm.reset();

            // Reset button
            setTimeout(() => {
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoader.style.display = 'none';
                document.getElementById('successMessage').style.display = 'none';
            }, 3000);
        } catch (error) {
            if (error.message !== 'Validation failed') {
                document.getElementById('errorMessage').textContent = error.message;
                document.getElementById('errorMessage').style.display = 'block';
            }

            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
        }
    });

    // Real-time validation
    const inputs = ['name', 'email', 'subject', 'message' , 'privacy'];
    inputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('blur', function() {
                const errors = validateForm({
                    [id]: this.value,
                    email: document.getElementById('email').value,
                    name: document.getElementById('name').value,
                    subject: document.getElementById('subject').value,
                    message: document.getElementById('message').value,
                    privacy: document.getElementById('privacy').checked
                });

                const errorEl = document.getElementById(id + 'Error');
                if (errorEl) {
                    if (errors[id]) {
                        errorEl.textContent = errors[id];
                        this.classList.add('error');
                    } else {
                        errorEl.textContent = '';
                        this.classList.remove('error');
                    }
                }
            });
        }
    });
}

// FAQ Accordion Logic
const faqQuestions = document.querySelectorAll(".faq-question");
faqQuestions.forEach((question) => {
    question.addEventListener("click", () => {
        const item = question.parentElement;
        const answer = item.querySelector(".faq-answer");

        item.classList.toggle("active");

        if (item.classList.contains("active")) {
            answer.style.maxHeight = answer.scrollHeight + "px";
        } else {
            answer.style.maxHeight = "0";
        }
    });
});