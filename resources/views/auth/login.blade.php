<x-layout title="{{ __('Log In') }} - {{ $settings->getTranslation('site_name') }}">
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
                    <h1>{{ $settings->getTranslation('login_title') }}</h1>
                    <p>{{ $settings->getTranslation('login_subtitle') }}</p>
                </div>

                <form class="login-form" id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">{{ __('Email') }}</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📧</span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="you@example.com" required autofocus autocomplete="username" />
                        </div>
                        @if ($errors->has('email') || $errors->has('credentials'))
                            <span class="error-message error-message--visible" role="alert">
                                {{ $errors->has('credentials') ? $errors->first('credentials') : $errors->first('email') }}
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="label-row">
                            <label for="password">{{ __('Password') }}</label>
                            <a href="{{ route('password.request') }}"
                                class="forgot-link">{{ __('Forgot your password?') }}</a>
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" placeholder="••••••••" required
                                autocomplete="current-password" />
                            <button type="button" class="toggle-password" id="togglePassword"
                                aria-label="{{ __('Toggle password visibility') }}" aria-pressed="false">
                                <span class="eye-icon" aria-hidden="true">👁️</span>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-message error-message--visible" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="rememberMe" name="remember" />
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login" id="loginBtn">
                        <span class="btn-text">{{ __('Log In') }}</span>
                        <span class="btn-loader" aria-hidden="true"></span>
                    </button>
                </form>

                <div class="signup-prompt">
                    <p>
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register.selection') }}">{{ __('Create New Account') }}</a>
                    </p>
                </div>
            </div>

                <div class="login-illustration"
                    style="{{ $settings->login_image ? 'background-image: url(' . Storage::url($settings->login_image) . ');' : '' }}">
                    <div class="illustration-content">
                        <div class="illustration-icon">❤️</div>
                        <h2>{{ $settings->getTranslation('login_title') }}</h2>
                        <p>{{ $settings->getTranslation('login_subtitle') }}</p>
                    <div class="stats-mini">
                        <div class="stat-mini">
                            <span class="stat-mini-number">{{ $stats['donors_count'] }}+</span>
                            <span class="stat-mini-label">{{ __('Donors') }}</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-number">{{ $stats['orgs_count'] }}</span>
                            <span class="stat-mini-label">{{ __('Hospitals') }}</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-number">{{ $stats['lives_saved'] }}</span>
                            <span class="stat-mini-label">{{ __('Lives Saved') }}</span>
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
