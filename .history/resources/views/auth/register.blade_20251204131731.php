<x-layout title="إنشاء حساب - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/login.css') }}" />
    @endpush

    <section class="login-section">
        <div class="login-background">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
        </div>

        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="welcome-icon">🆕</div>
                    <h1>إنشاء حساب جديد</h1>
                    <p>انضم إلينا وأنقذ الأرواح</p>
                </div>

                <form class="login-form" id="registerForm" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name">الاسم الكامل</label>
                        <div class="input-wrapper">
                            <span class="input-icon">👤</span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="أدخل اسمك الكامل" required autofocus autocomplete="name" />
                        </div>
                        @error('name')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📧</span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="you@example.com" required autocomplete="username" />
                        </div>
                        @error('email')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">كلمة السر</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" placeholder="••••••••" required
                                autocomplete="new-password" />
                            <button type="button" class="toggle-password" id="togglePassword">
                                <span class="eye-icon">👁️</span>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">تأكيد كلمة السر</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔐</span>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required
                                autocomplete="new-password" />
                            <button type="button" class="toggle-password" id="togglePasswordConfirm">
                                <span class="eye-icon">👁️</span>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <span class="btn-text">إنشاء الحساب</span>
                        <span class="btn-loader"></span>
                    </button>
                </form>

                <div class="signup-prompt">
                    <p>
                        لديك حساب بالفعل؟
                        <a href="{{ route('login') }}">تسجيل الدخول</a>
                    </p>
                </div>
            </div>

            <div class="login-illustration">
                <div class="illustration-content">
                    <div class="illustration-icon">🩸</div>
                    <h2>ابدأ رحلتك الآن</h2>
                    <p>كن جزءاً من مجتمع المتبرعين الأبطال</p>
                    <div class="stats-mini">
                        <div class="stat-mini">
                            <span class="stat-mini-number">5000+</span>
                            <span class="stat-mini-label">متبرع نشط</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-number">120</span>
                            <span class="stat-mini-label">مستشفى شريك</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-number">4500</span>
                            <span class="stat-mini-label">حياة منقذة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                initPasswordToggle();
            });

            function initPasswordToggle() {
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');
                if (togglePassword && passwordInput) {
                    const eyeIcon = togglePassword.querySelector('.eye-icon');
                    togglePassword.addEventListener('click', () => {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        eyeIcon.textContent = type === 'password' ? '👁️' : '🙈';
                    });
                }

                const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
                const passwordConfirmInput = document.getElementById('password_confirmation');
                if (togglePasswordConfirm && passwordConfirmInput) {
                    const eyeIconConfirm = togglePasswordConfirm.querySelector('.eye-icon');
                    togglePasswordConfirm.addEventListener('click', () => {
                        const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordConfirmInput.setAttribute('type', type);
                        eyeIconConfirm.textContent = type === 'password' ? '👁️' : '🙈';
                    });
                }
            }
        </script>
    @endpush
</x-layout>
