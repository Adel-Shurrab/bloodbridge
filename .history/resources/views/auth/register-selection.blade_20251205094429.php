<x-public-layout title="إنشاء حساب جديد - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/registration-intro.css') }}" />
    @endpush

    <section class="registration-section">
        <div class="registration-background">
            <div class="floating-circle circle-1"></div>
            <div class="floating-circle circle-2"></div>
            <div class="floating-circle circle-3"></div>
        </div>

        <div class="registration-container">
            <div class="registration-header">
                <div class="header-badge">ابدأ رحلتك</div>
                <h1>انضم إلى مجتمعنا</h1>
                <p>
                    اختر طريقك لتبدأ بإحداث فرق. خطوتك الأولى تساعدنا على ربط المنقذين
                    بالمحتاجين
                </p>
            </div>

            <div class="options-container">
                <div class="option-card" id="donor-card" onclick="selectOption('donor')">
                    <div class="option-icon donor-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                    </div>
                    <div class="option-content">
                        <h3>أنا متبرع</h3>
                        <p>
                            سجل للتبرع بالدم، وإدارة المواعيد، وشاهد تأثير مساهماتك في إنقاذ
                            الأرواح
                        </p>
                        <ul class="option-features">
                            <li><span class="feature-icon">✓</span><span>إدارة مواعيد التبرع</span></li>
                            <li><span class="feature-icon">✓</span><span>تتبع سجل التبرعات</span></li>
                            <li><span class="feature-icon">✓</span><span>إشعارات فورية بالطلبات</span></li>
                        </ul>
                    </div>
                    <div class="option-checkmark">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>

                <div class="option-card" id="organization-card" onclick="selectOption('organization')">
                    <div class="option-icon organization-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <div class="option-content">
                        <h3>أنا منظمة</h3>
                        <p>
                            سجل مؤسستك الصحية لاستضافة حملات التبرع بالدم وتنسيق فعاليات
                            التبرع المجتمعية
                        </p>
                        <ul class="option-features">
                            <li><span class="feature-icon">✓</span><span>إنشاء طلبات تبرع</span></li>
                            <li><span class="feature-icon">✓</span><span>إدارة الحملات</span></li>
                            <li><span class="feature-icon">✓</span><span>تقارير وإحصائيات</span></li>
                        </ul>
                    </div>
                    <div class="option-checkmark">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="action-container">
                <a id="continueBtn" href="#" class="btn btn-primary btn-continue disabled">
                    <span>متابعة</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </a>
                <p class="login-prompt">
                    لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a>
                </p>
            </div>

            <div class="trust-indicators">
                <div class="trust-item">
                    <span class="trust-icon">🔒</span>
                    <span class="trust-text">آمن ومشفر</span>
                </div>
                <div class="trust-item">
                    <span class="trust-icon">✓</span>
                    <span class="trust-text">موثوق من 120+ مستشفى</span>
                </div>
                <div class="trust-item">
                    <span class="trust-icon">⚡</span>
                    <span class="trust-text">تسجيل في دقائق</span>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            // Simple logic to handle selection
            let selectedType = null;
            const continueBtn = document.getElementById('continueBtn');
            const donorCard = document.getElementById('donor-card');
            const orgCard = document.getElementById('organization-card');

            function selectOption(type) {
                selectedType = type;
                
                // Remove active class from all
                donorCard.classList.remove('active', 'selected');
                orgCard.classList.remove('active', 'selected'); // 'selected' class might be used in your CSS
                
                // Add active class to clicked
                if (type === 'donor') {
                    donorCard.classList.add('active', 'selected');
                    // Update Link using Laravel Route
                    continueBtn.href = "{{ route('register.donor') }}";
                } else {
                    orgCard.classList.add('active', 'selected');
                    // Update Link using Laravel Route
                    continueBtn.href = "{{ route('register.organization') }}";
                }

                // Enable button
                continueBtn.classList.remove('disabled');
                continueBtn.style.pointerEvents = 'auto';
                continueBtn.style.opacity = '1';
            }
        </script>
    @endpush
</x-public-layout>