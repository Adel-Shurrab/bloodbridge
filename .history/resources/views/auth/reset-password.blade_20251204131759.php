<x-layout title="إعادة تعيين كلمة السر - BloodBridge">
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
                    <div class="welcome-icon">🔐</div>
                    <h1>إعادة تعيين كلمة السر</h1>
                    <p>أنشئ كلمة سر قوية وجديدة</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="login-form">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📧</span>
                            <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}"
                                placeholder="you@example.com" required autofocus autocomplete="username" />
                        </div>
                        @error('email')
                            <span class="error-message" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">كلمة السر الجديدة</label>
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
                        <span class="btn-text">إعادة تعيين كلمة السر</span>
                        <span class="btn-loader"></span>
                    </button>
                </form>
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
