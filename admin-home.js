// Mobile Menu Functionality
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const sidebar = document.getElementById('sidebar');

if (mobileMenuBtn && sidebar) {
    mobileMenuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', (e) => {
        if (sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            !mobileMenuBtn.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}

// Modal Functionality
const reviewModal = document.getElementById('review-modal');
const closeModal = document.getElementById('close-modal');
const modalBody = document.getElementById('modal-body');
const approveBtn = document.getElementById('approve-btn');
const rejectBtn = document.getElementById('reject-btn');

let currentOrganization = '';

// Review buttons
const reviewButtons = document.querySelectorAll('.btn-review');

reviewButtons.forEach(button => {
    button.addEventListener('click', () => {
        const orgName = button.dataset.org;
        currentOrganization = orgName;
        
        // Sample data - would come from backend in real application
        const orgData = {
            'مركز التبرع بالدم المجتمعي': {
                email: 'community.bloodcenter.org',
                contact: 'سارة جونسون',
                role: 'مديرة',
                date: '15 نوفمبر 2023',
                phone: '+970-59-123-4567',
                address: 'شارع الجامعة، نابلس'
            },
            'بنك الدم الإقليمي': {
                email: 'contact@regionalbb.com',
                contact: 'مايكل سميث',
                role: 'منسق',
                date: '10 نوفمبر 2023',
                phone: '+970-59-234-5678',
                address: 'شارع فيصل، رام الله'
            },
            'خدمات الدم بمستشفى المدينة': {
                email: 'info@cityhospitalbs.net',
                contact: 'إميلي وايت',
                role: 'مديرة',
                date: '5 نوفمبر 2023',
                phone: '+970-59-345-6789',
                address: 'شارع المستشفى، الخليل'
            }
        };

        const data = orgData[orgName];

        // Create modal content
        const modalContent = `
            <div class="modal-detail-grid">
                <div class="modal-detail-item full-width">
                    <span class="modal-detail-label">اسم المؤسسة</span>
                    <span class="modal-detail-value">${orgName}</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">البريد الإلكتروني</span>
                    <span class="modal-detail-value">${data.email}</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">رقم الهاتف</span>
                    <span class="modal-detail-value">${data.phone}</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">جهة الاتصال</span>
                    <span class="modal-detail-value">${data.contact}</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">المنصب</span>
                    <span class="modal-detail-value">${data.role}</span>
                </div>
                <div class="modal-detail-item full-width">
                    <span class="modal-detail-label">العنوان</span>
                    <span class="modal-detail-value">${data.address}</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">تاريخ التسجيل</span>
                    <span class="modal-detail-value">${data.date}</span>
                </div>
                <div class="modal-detail-item">
                    <span class="modal-detail-label">الحالة</span>
                    <span class="modal-detail-value">قيد المراجعة</span>
                </div>
            </div>
        `;

        modalBody.innerHTML = modalContent;
        reviewModal.classList.add('active');
    });
});

// Close modal
if (closeModal) {
    closeModal.addEventListener('click', () => {
        reviewModal.classList.remove('active');
    });
}

// Close modal when clicking outside
if (reviewModal) {
    reviewModal.addEventListener('click', (e) => {
        if (e.target === reviewModal) {
            reviewModal.classList.remove('active');
        }
    });
}

// Approve button
if (approveBtn) {
    approveBtn.addEventListener('click', () => {
        // Add loading state
        const originalHTML = approveBtn.innerHTML;
        approveBtn.innerHTML = '<span>⏳</span><span>جاري المعالجة...</span>';
        approveBtn.disabled = true;
        rejectBtn.disabled = true;

        setTimeout(() => {
            reviewModal.classList.remove('active');
            
            // Remove row from table
            const rows = document.querySelectorAll('.table-row');
            rows.forEach(row => {
                const btn = row.querySelector('.btn-review');
                if (btn && btn.dataset.org === currentOrganization) {
                    row.style.animation = 'fadeOutRow 0.5s ease-out forwards';
                    setTimeout(() => {
                        row.remove();
                        updateStats();
                    }, 500);
                }
            });

            showNotification(`تمت الموافقة على ${currentOrganization} بنجاح`, 'success');
            
            // Reset buttons
            approveBtn.innerHTML = originalHTML;
            approveBtn.disabled = false;
            rejectBtn.disabled = false;
        }, 1500);
    });
}

// Reject button
if (rejectBtn) {
    rejectBtn.addEventListener('click', () => {
        // Add loading state
        const originalHTML = rejectBtn.innerHTML;
        rejectBtn.innerHTML = '<span>⏳</span><span>جاري المعالجة...</span>';
        rejectBtn.disabled = true;
        approveBtn.disabled = true;

        setTimeout(() => {
            reviewModal.classList.remove('active');
            
            // Remove row from table
            const rows = document.querySelectorAll('.table-row');
            rows.forEach(row => {
                const btn = row.querySelector('.btn-review');
                if (btn && btn.dataset.org === currentOrganization) {
                    row.style.animation = 'fadeOutRow 0.5s ease-out forwards';
                    setTimeout(() => {
                        row.remove();
                        updateStats();
                    }, 500);
                }
            });

            showNotification(`تم رفض ${currentOrganization}`, 'error');
            
            // Reset buttons
            rejectBtn.innerHTML = originalHTML;
            rejectBtn.disabled = false;
            approveBtn.disabled = false;
        }, 1500);
    });
}

// Add fade out animation
const fadeOutStyle = document.createElement('style');
fadeOutStyle.textContent = `
    @keyframes fadeOutRow {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.9);
        }
    }
`;
document.head.appendChild(fadeOutStyle);

// Update statistics
function updateStats() {
    const pendingCount = document.querySelectorAll('.table-row').length;
    const pendingValue = document.querySelector('.stat-card.primary .stat-card-value');
    
    if (pendingValue) {
        pendingValue.style.animation = 'pulse 0.5s ease-out';
        pendingValue.textContent = pendingCount;
        
        setTimeout(() => {
            pendingValue.style.animation = '';
        }, 500);
    }
}

// Pulse animation
const pulseStyle = document.createElement('style');
pulseStyle.textContent = `
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
        }
    }
`;
document.head.appendChild(pulseStyle);

// View All button
const viewAllBtn = document.getElementById('view-all-btn');
if (viewAllBtn) {
    viewAllBtn.addEventListener('click', () => {
        showNotification('سيتم إعادة توجيهك إلى صفحة التحقق الكاملة', 'info');
        
        // Simulate navigation
        setTimeout(() => {
            console.log('Navigate to verification page');
        }, 1500);
    });
}

// Notification Function
function showNotification(message, type = 'success') {
    // Remove existing notification if any
    const existingNotification = document.querySelector('.notification-toast');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast ${type}`;

    const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
    notification.innerHTML = `
        <span style="font-size: 1.5rem;">${icon}</span>
        <span>${message}</span>
    `;

    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideUp 0.4s ease-out forwards';
        setTimeout(() => {
            notification.remove();
        }, 400);
    }, 3000);
}

// Add slide up animation
const slideUpStyle = document.createElement('style');
slideUpStyle.textContent = `
    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        to {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
    }
`;
document.head.appendChild(slideUpStyle);

// Notification Button Animation
const notificationBtn = document.querySelector('.notification-btn');
if (notificationBtn) {
    notificationBtn.addEventListener('click', () => {
        notificationBtn.style.animation = 'none';
        setTimeout(() => {
            notificationBtn.style.animation = 'bounce 0.5s ease';
        }, 10);

        showNotification('لديك إشعاران جديدان', 'info');
    });

    // Add bounce animation
    const bounceStyle = document.createElement('style');
    bounceStyle.textContent = `
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(0.9); }
            50% { transform: scale(1.1); }
            75% { transform: scale(0.95); }
        }
    `;
    document.head.appendChild(bounceStyle);
}

// Animate rows on load
function animateRows() {
    const rows = document.querySelectorAll('.table-row');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';

        setTimeout(() => {
            row.style.transition = 'all 0.5s ease-out';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Call animation on page load
window.addEventListener('load', () => {
    animateRows();
});

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // ESC to close modal
    if (e.key === 'Escape' && reviewModal.classList.contains('active')) {
        reviewModal.classList.remove('active');
    }
});

// Add hover effects
document.querySelectorAll('.stat-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        const icon = card.querySelector('.stat-card-icon');
        if (icon) {
            icon.style.transition = 'all 0.3s ease';
        }
    });
});

// Initialize page
console.log('Admin Home Page initialized');
console.log('Pending verifications:', document.querySelectorAll('.table-row').length);

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Stats animation on scroll
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'slideInUp 0.6s ease-out';
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.stat-card').forEach(card => {
    statsObserver.observe(card);
});

// Profile section interaction
const profileSection = document.querySelector('.profile-section');
if (profileSection) {
    profileSection.addEventListener('click', () => {
        showNotification('سيتم إعادة توجيهك إلى الملف الشخصي', 'info');
    });
}

console.log('All event listeners attached successfully');