<x-layout title="{{ __('Forgot Password') }} - {{ $settings->getTranslation('site_name') }}">
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
                    <h1>{{ __('Forgot Your Password?') }}</h1>
                    <p>{{ __('No worries! Enter your email and we\'ll send you a reset link.') }}</p>
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
                        <label for="email">{{ __('Email Address') }}</label>
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
                        <span class="btn-text">{{ __('Send Reset Link') }}</span>
                        <span class="btn-loader"></span>
                    </button>
                </form>

                {{-- Back to login --}}
                <div class="fp-back-link">
                    <p>
                        {{ __('Remembered your password?') }}
                        <a href="{{ route('login') }}">
                            <i class="fa-solid fa-arrow-right"></i>
                            {{ __('Back to Login') }}
                        </a>
                    </p>
                </div>
            </div>

            {{-- ── Illustration Panel ── --}}
            <div class="fp-illustration">
                <div class="fp-illustration-content">

                    <span class="fp-illustration-icon">🔐</span>

                    <h2>{{ __('Restore Access to Your Account') }}</h2>
                    <p>{{ __('Account security is our priority. Follow the simple steps to securely reset your password.') }}
                    </p>

                    <div class="fp-steps">
                        <div class="fp-step">
                            <span class="fp-step-num">1</span>
                            <span class="fp-step-text">{{ __('Enter your registered email address') }}</span>
                        </div>
                        <div class="fp-step">
                            <span class="fp-step-num">2</span>
                            <span class="fp-step-text">{{ __('Check your email for the reset link') }}</span>
                        </div>
                        <div class="fp-step">
                            <span class="fp-step-num">3</span>
                            <span class="fp-step-text">{{ __('Click the link and create a new password') }}</span>
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
