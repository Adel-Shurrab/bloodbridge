<x-layout title="{{ __('Confirm Password') }} - {{ $settings->getTranslation('site_name') }}">
    <style>
        .confirm-password-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8fafc;
        }

        .confirm-password-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }

        .confirm-password-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .confirm-password-header i {
            font-size: 3rem;
            color: #d32f2f;
            margin-bottom: 1rem;
        }

        .confirm-password-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .confirm-password-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #334155;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-inline-start: 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #d32f2f;
            box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.1);
        }

        .input-icon {
            position: absolute;
            inset-inline-start: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .submit-btn {
            width: 100%;
            padding: 0.875rem;
            background: #d32f2f;
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            background: #b71c1c;
            transform: translateY(-1px);
        }
    </style>

    <section class="confirm-password-section">
        <div class="confirm-password-card">
            <div class="confirm-password-header">
                <i class="fa-solid fa-user-shield"></i>
                <h1>{{ __('Confirm Password') }}</h1>
                <p>{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••" />
                        <i class="fa-solid fa-lock input-icon"></i>
                    </div>
                    @error('password')
                        <p style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">
                    <span>{{ __('Confirm') }}</span>
                    <i class="fa-solid fa-check"></i>
                </button>
            </form>
        </div>
    </section>
</x-layout>
