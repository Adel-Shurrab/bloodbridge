<x-layout title="إعادة تعيين كلمة المرور - {{ $settings->site_name }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/reset-password.css') }}" />
    @endpush

    <section class="rp-section">

        {{-- Animated background blobs --}}
        <div class="rp-background">
            <div class="rp-blob rp-blob-1"></div>
            <div class="rp-blob rp-blob-2"></div>
            <div class="rp-blob rp-blob-3"></div>
        </div>

        <div class="rp-container">

            {{-- ── Form Card ── --}}
            <div class="rp-card">

                {{-- Header --}}
                <div class="rp-header">
                    <div class="rp-icon-wrap">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h1>إعادة تعيين كلمة المرور</h1>
                    <p>الرجاء إدخال بريدك الإلكتروني وكلمة المرور الجديدة لحسابك.</p>
                </div>

                {{-- Form --}}
                <form class="rp-form" method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="rp-form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="rp-input-wrapper">
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $request->email) }}" placeholder="you@example.com" required
                                autofocus autocomplete="username"
                                class="{{ $errors->has('email') ? 'is-invalid' : '' }}" />
                            <i class="fa-solid fa-envelope rp-input-icon"></i>
                        </div>
                        @error('email')
                            <p class="rp-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="rp-form-group">
                        <label for="password">كلمة المرور الجديدة</label>
                        <div class="rp-input-wrapper">
                            <input type="password" id="password" name="password" placeholder="••••••••" required
                                autocomplete="new-password"
                                class="{{ $errors->has('password') ? 'is-invalid' : '' }}" />
                            <i class="fa-solid fa-lock rp-input-icon"></i>
                            <button type="button" class="rp-toggle-btn" aria-label="تبديل رؤية كلمة المرور">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>

                        <!-- Optional Password Strength Indicator -->
                        <div class="rp-strength" id="passwordStrength" style="display: none;">
                            <div class="rp-strength-bar">
                                <div class="rp-strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="rp-strength-text" id="strengthText">ضعيفة</span>
                        </div>

                        @error('password')
                            <p class="rp-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="rp-form-group">
                        <label for="password_confirmation">تأكيد كلمة المرور</label>
                        <div class="rp-input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="••••••••" required autocomplete="new-password"
                                class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" />
                            <i class="fa-solid fa-shield-halved rp-input-icon"></i>
                            <button type="button" class="rp-toggle-btn" aria-label="تبديل رؤية كلمة المرور">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="rp-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit" class="rp-submit-btn" id="submitBtn">
                        <i class="fa-solid fa-floppy-disk rp-btn-text"></i>
                        <span class="rp-btn-text">حفظ كلمة المرور الجديدة</span>
                        <span class="rp-btn-loader"></span>
                    </button>
                </form>

                {{-- Back to login --}}
                <div class="rp-back-link">
                    <p>
                        تذكرت كلمة المرور؟
                        <a href="{{ route('login') }}">
                            <i class="fa-solid fa-arrow-right"></i>
                            العودة لتسجيل الدخول
                        </a>
                    </p>
                </div>
            </div>

            {{-- ── Illustration Panel ── --}}
            <div class="rp-illustration">
                <div class="rp-illustration-content">

                    <span class="rp-illustration-icon">🔐</span>

                    <h2>تأمين حسابك</h2>
                    <p>اختر كلمة مرور قوية لحماية بياناتك. نوصي باستخدام مزيج من الأحرف والأرقام والرموز.</p>

                    <div class="rp-rules">
                        <div class="rp-rule">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>٨ أحرف على الأقل</span>
                        </div>
                        <div class="rp-rule">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>حرف كبير وحرف صغير</span>
                        </div>
                        <div class="rp-rule">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>رقم واحد على الأقل</span>
                        </div>
                        <div class="rp-rule">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>رمز خاص واحد (!@#$)</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Toggle Password Visibility
                const toggleBtns = document.querySelectorAll('.rp-toggle-btn');
                toggleBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const input = this.previousElementSibling
                        .previousElementSibling; // The input field
                        const icon = this.querySelector('i');

                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                            icon.style.color = '#d32f2f'; // Active color
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                            icon.style.color = ''; // Reset to CSS default
                        }
                    });
                });

                // Simple Password Strength Indicator
                const passwordInput = document.getElementById('password');
                const strengthContainer = document.getElementById('passwordStrength');
                const fillBar = document.getElementById('strengthFill');
                const textLabel = document.getElementById('strengthText');

                if (passwordInput && strengthContainer) {
                    passwordInput.addEventListener('input', function() {
                        const val = this.value;
                        if (val.length > 0) {
                            strengthContainer.style.display = 'block';

                            let strength = 0;
                            if (val.length >= 8) strength += 25;
                            if (val.match(/[A-Z]/) && val.match(/[a-z]/)) strength += 25;
                            if (val.match(/[0-9]/)) strength += 25;
                            if (val.match(/[^a-zA-Z0-9]/)) strength += 25;

                            fillBar.style.width = strength + '%';

                            if (strength <= 25) {
                                fillBar.style.background = '#ef4444'; // Red
                                textLabel.textContent = 'ضعيفة جداً';
                                textLabel.style.color = '#ef4444';
                            } else if (strength <= 50) {
                                fillBar.style.background = '#f59e0b'; // Orange
                                textLabel.textContent = 'ضعيفة';
                                textLabel.style.color = '#f59e0b';
                            } else if (strength <= 75) {
                                fillBar.style.background = '#10b981'; // Green
                                textLabel.textContent = 'جيدة';
                                textLabel.style.color = '#10b981';
                            } else {
                                fillBar.style.background = '#059669'; // Dark Green
                                textLabel.textContent = 'قوية';
                                textLabel.style.color = '#059669';
                            }
                        } else {
                            strengthContainer.style.display = 'none';
                            fillBar.style.width = '0%';
                        }
                    });
                }

                // Loading state on submit
                const form = document.getElementById('resetPasswordForm');
                const btn = document.getElementById('submitBtn');

                if (form && btn) {
                    form.addEventListener('submit', () => {
                        // Only show loading if form is valid enough to submit natively
                        btn.classList.add('loading');
                        btn.disabled = true;
                    });
                }
            });
        </script>
    @endpush
</x-layout>
