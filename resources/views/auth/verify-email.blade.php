<x-layout title="{{ __('Verify Email') }} - {{ $settings->getTranslation('site_name') }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/verify-email.css') }}" />
    @endpush

    <section class="ve-section">

        {{-- Animated background blobs --}}
        <div class="ve-background">
            <div class="ve-blob ve-blob-1"></div>
            <div class="ve-blob ve-blob-2"></div>
        </div>

        <div class="ve-container">

            {{-- ── Form Card ── --}}
            <div class="ve-card">

                {{-- Header --}}
                <div class="ve-header">
                    <div class="ve-icon-wrap">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <h1>{{ __('Verify Email Address') }}</h1>
                    <p>
                        {{ __('Thanks for joining us! Before getting started, please verify your email address by clicking on the link we just sent you. If you didn\'t receive the email, we will gladly send you another one.') }}
                    </p>
                </div>

                {{-- Success status alert --}}
                @if (session('status') == 'verification-link-sent')
                    <div class="ve-status-alert" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</span>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="ve-actions">
                    <form method="POST" action="{{ route('verification.send') }}" id="resendVerificationForm">
                        @csrf
                        <button type="submit" class="ve-submit-btn" id="submitBtn">
                            <i class="fa-solid fa-paper-plane btn-text"></i>
                            <span class="btn-text">{{ __('Resend Verification Link') }}</span>
                            <span class="btn-loader"></span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="ve-logout-form">
                        @csrf
                        <button type="submit" class="ve-logout-btn">
                            {{ __('Log in with another account?') }}
                        </button>
                    </form>
                </div>

            </div>

            {{-- ── Illustration Panel ── --}}
            <div class="ve-illustration">
                <div class="ve-illustration-content">

                    <span class="ve-illustration-icon">🛡️</span>

                    <h2>{{ __('Account Activation and Security') }}</h2>
                    <p>{{ __('To protect your data and ensure effective communication, we request confirmation of your email ownership.') }}
                    </p>

                    <div class="ve-steps">
                        <div class="ve-step">
                            <span class="ve-step-num">1</span>
                            <span class="ve-step-text">{{ __('Open your email inbox') }}</span>
                        </div>
                        <div class="ve-step">
                            <span class="ve-step-num">2</span>
                            <span
                                class="ve-step-text">{{ __('Look for the activation message from our platform') }}</span>
                        </div>
                        <div class="ve-step">
                            <span class="ve-step-num">3</span>
                            <span
                                class="ve-step-text">{{ __('Click on the verification button to get started') }}</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    @push('scripts')
        <script>
            // Loading state on submit
            const form = document.getElementById('resendVerificationForm');
            const btn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', () => {
                    btn.classList.add('loading');
                    btn.disabled = true;
                });
            }
        </script>
    @endpush
</x-layout>
