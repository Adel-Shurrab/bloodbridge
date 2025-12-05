<x-layout title="تسجيل الدخول - BloodBridge">
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
                    <div class="welcome-icon">👋</div>
                    <h1>مرحبًا بعودتك</h1>
                    <p>سجل دخولك للوصول إلى حسابك</p>
                </div>

                <form class="login-form" id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📧</span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="you@example.com" required autofocus autocomplete="username" />
                        </div>
                        @if ($errors->has('email') && !str_contains($errors->first('email'), 'credentials'))
                            <span class="error-message">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="label-row">
                            <label for="password">كلمة السر</label>
                            <a href="{{ route('password.request') }}" class="forgot-link">هل نسيت كلمة السر؟</a>
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" placeholder="••••••••" required
                                autocomplete="current-password" />
                            <button type="button" class="toggle-password" id="togglePassword">
                                <span class="eye-icon">👁️</span>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label" for="rememberMe">
                            <input 
                                type="checkbox" 
                                id="rememberMe" 
                                name="remember" 
                                value="1"
                                @if(old('remember'))
                                    checked
                                @endif
                                aria-label="تذكرني في هذا الجهاز لمدة 60 يوم"
                                aria-describedby="remember-help"
                            />
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">تذكرني</span>
                        </label>
                        <small id="remember-help" class="remember-help">
                            سيبقى حسابك مسجل الدخول لمدة 60 يوم على هذا الجهاز فقط
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <span class="btn-text">تسجيل الدخول</span>
                        <span class="btn-loader"></span>
                    </button>
                </form>

                <div class="divider">
                    <span>أو</span>
                </div>

                <button class="btn btn-social">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4" />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853" />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05" />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335" />
                    </svg>
                    <span>تواصل مع جوجل</span>
                </button>

                <div class="signup-prompt">
                    <p>
                        ليس لديك حساب؟
                        <a href="{{ route('register.selection') }}">إنشاء حساب جديد</a>
                    </p>
                </div>
            </div>

            <div class="login-illustration">
                <div class="illustration-content">
                    <div class="illustration-icon">❤️</div>
                    <h2>انضم إلى مجتمعنا</h2>
                    <p>أكثر من 5000 متبرع ينقذون الأرواح يوميًا</p>
                    <div class="stats-mini">
                        <div class="stat-mini">
                            <span class="stat-mini-number">5000+</span>
                            <span class="stat-mini-label">متبرع</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-number">120</span>
                            <span class="stat-mini-label">مستشفى</span>
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
        <script src="{{ asset('assets/scripts/pages/login.js') }}"></script>
    @endpush
</x-layout>