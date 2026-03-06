<x-layout title="نسيت كلمة المرور - {{ $settings->site_name }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/forgot-password.css') }}" />
    @endpush

    <section class="fp-section">

        {{-- Animated background blobs --}}
        <div class="fp-background">
            <div class="fp-blob fp-blob-1"></div>
            <div class="fp-blob fp-blob-2"></div>
            <div class="fp-blob fp-blob-3"></div>
        </div>

        <div class="fp-container">

            {{-- ── Form Card ── --}}
            <div class="fp-card">

                {{-- Header --}}
                <div class="fp-header">
                    <div class="fp-icon-wrap">
                        <i class="fa-solid fa-lock-open"></i>
                    </div>
                    <h1>نسيت كلمة المرور؟</h1>
                    <p>لا تقلق! أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة تعيين كلمة المرور.</p>
                </div>

                {{-- Success status alert --}}
                @if (session('status'))
                    <div class="fp-status-alert" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Form --}}
                <form class="fp-form" method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                    @csrf

                    <div class="fp-form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="fp-input-wrapper">
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="you@example.com" required autofocus autocomplete="email"
                                class="{{ $errors->has('email') ? 'is-invalid' : '' }}" />
                            <i class="fa-solid fa-envelope fp-input-icon"></i>
                        </div>
                        @error('email')
                            <p class="fp-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit" class="fp-submit-btn" id="submitBtn">
                        <i class="fa-solid fa-paper-plane btn-text"></i>
                        <span class="btn-text">إرسال رابط إعادة التعيين</span>
                        <span class="btn-loader"></span>
                    </button>
                </form>

                {{-- Back to login --}}
                <div class="fp-back-link">
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
            <div class="fp-illustration">
                <div class="fp-illustration-content">

                    <span class="fp-illustration-icon">🔐</span>

                    <h2>استعادة الوصول لحسابك</h2>
                    <p>أمان حسابك أولويتنا. اتبع الخطوات البسيطة لإعادة تعيين كلمة مرورك بأمان.</p>

                    <div class="fp-steps">
                        <div class="fp-step">
                            <span class="fp-step-num">١</span>
                            <span class="fp-step-text">أدخل بريدك الإلكتروني المسجّل</span>
                        </div>
                        <div class="fp-step">
                            <span class="fp-step-num">٢</span>
                            <span class="fp-step-text">تحقق من بريدك لرسالة التعيين</span>
                        </div>
                        <div class="fp-step">
                            <span class="fp-step-num">٣</span>
                            <span class="fp-step-text">انقر على الرابط وأنشئ كلمة مرور جديدة</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    @push('scripts')
        <script>
            // Loading state on submit
            const form = document.getElementById('forgotPasswordForm');
            const btn = document.getElementById('submitBtn');

            form.addEventListener('submit', () => {
                btn.classList.add('loading');
                btn.disabled = true;
            });
        </script>
    @endpush
</x-layout>
