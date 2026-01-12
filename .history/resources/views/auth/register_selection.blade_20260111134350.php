<x-layout title="إنشاء حساب جديد - BloodBridge">
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
                <h1>{{ $settings->signup_title }}</h1>
                <p>{{ $settings->signup_subtitle }}</p>
            </div>

            <div class="options-container">
                <div class="option-card" id="donor-card" role="button" tabindex="0" aria-label="تسجيل كمتبرع">
                    <div class="option-icon donor-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                    </div>
                    <div class="option-content">
                        <h3>{{ $settings->signup_donor_title }}</h3>
                        <p>{{ $settings->signup_donor_subtitle }}</p>
                        <ul class="option-features">
                            @foreach($settings->signup_donor_tasks ?? [] as $task)
                                <li><span class="feature-icon">✓</span><span>{{ $task }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="option-checkmark" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>

                <div class="option-card" id="organization-card" role="button" tabindex="0" aria-label="تسجيل كمنظمة">
                    <div class="option-icon organization-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <div class="option-content">
                        <h3>{{ $settings->signup_org_title }}</h3>
                        <p>{{ $settings->signup_org_subtitle }}</p>
                        <ul class="option-features">
                            @foreach($settings->signup_org_tasks ?? [] as $task)
                                <li><span class="feature-icon">✓</span><span>{{ $task }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="option-checkmark" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="action-container">
                <button id="continueBtn" class="btn btn-primary btn-continue disabled" disabled
                    aria-label="متابعة إلى صفحة التسجيل">
                    <span>متابعة</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </button>
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
        <script src="{{ asset('assets/scripts/pages/registration-intro.js') }}"></script>
    @endpush
</x-layout>