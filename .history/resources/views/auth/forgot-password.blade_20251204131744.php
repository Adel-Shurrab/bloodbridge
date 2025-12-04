<x-layout title="نسيان كلمة السر - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/login.css') }}" />
    @endpush

    <section class="login-section">
        <div class="login-background">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
        </div>

        <div class="login-container" style="grid-template-columns: 1fr;">
            <div class="login-card">
                <div class="login-header">
                    <div class="welcome-icon">🔑</div>
                    <h1>نسيت كلمة السر؟</h1>
                    <p>لا تقلق، سنساعدك في استرجاع حسابك</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #10b981;">
                        {{ session('status') }}
                    </div>
                @endif

                <p style="color: #6b7280; margin-bottom: 1.5rem; text-align: center;">
                    أدخل بريدك الإلكتروني وسنرسل لك رابط استرجاع كلمة السر
                </p>

                <form method="POST" action="{{ route('password.email') }}" class="login-form">
                    @csrf

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📧</span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="you@example.com" required autofocus />
                        </div>
                        @error('email')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <span class="btn-text">إرسال رابط الاسترجاع</span>
                        <span class="btn-loader"></span>
                    </button>
                </form>

                <div class="signup-prompt">
                    <p>
                        <a href="{{ route('login') }}">العودة لتسجيل الدخول</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-layout>
