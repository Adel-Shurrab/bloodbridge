@props(['name' => 'eligibilityModal'])

<x-modal :name="$name" maxWidth="2xl">
    <div class="eligibility-modal-container"
        style="background: #ffffff; color: #2d3748; font-family: 'Cairo', sans-serif; position: relative; overflow: hidden; width: 100%;">

        <!-- Decorative Background Element -->
        <div
            style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(56, 161, 105, 0.04); border-radius: 50%; z-index: 0;">
        </div>

        <style>
            .eligibility-modal-container {
                padding: 1.5rem;

                text-align: start;
            }

            .eligibility-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #f0f4f8;
                position: relative;
                z-index: 1;
            }

            .eligibility-modal-title {
                font-size: 1.5rem;
                font-weight: 800;
                color: #2d3748;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin: 0;
            }

            .eligibility-modal-title i {
                color: #38a169;
            }

            .eligibility-modal-close {
                background: #f7fafc;
                border: none;
                color: #718096;
                cursor: pointer;
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .eligibility-modal-close:hover {
                background: #edf2f7;
                color: #2d3748;
                transform: rotate(90deg);
            }

            .eligibility-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1.25rem;
                margin-bottom: 1.5rem;
                position: relative;
                z-index: 1;
            }

            .eligibility-card {
                background: #f8fafc;
                border: 1px solid #edf2f7;
                padding: 1rem;
                border-radius: 12px;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                transition: all 0.3s ease;
            }

            .eligibility-card:hover {
                border-color: #38a169;
                background: #fff;
                box-shadow: 0 4px 12px rgba(56, 161, 105, 0.08);
            }

            .eligibility-card-icon {
                width: 32px;
                height: 32px;
                background: #f0fff4;
                color: #38a169;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.9rem;
            }

            .eligibility-card h4 {
                margin: 0;
                font-size: 1rem;
                font-weight: 700;
                color: #1a202c;
            }

            .eligibility-card p {
                margin: 0;
                font-size: 0.85rem;
                color: #4a5568;
                line-height: 1.5;
            }

            .eligibility-notice {
                background: #fffaf0;
                border-inline-start: 4px solid #ed8936;
                padding: 1rem;
                border-radius: 4px 8px 8px 4px;
                font-size: 0.85rem;
                color: #744210;
                margin-bottom: 1.5rem;
                line-height: 1.6;
            }

            .eligibility-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 1rem;
                border-top: 2px solid #f0f4f8;
            }

            .view-more-link {
                color: #3182ce;
                font-weight: 700;
                text-decoration: none;
                font-size: 0.9rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .view-more-link:hover {
                text-decoration: underline;
                color: #2c5282;
            }

            .close-btn {
                background: #38a169;
                color: white;
                border: none;
                padding: 0.6rem 1.5rem;
                border-radius: 10px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Cairo', sans-serif;
            }

            .close-btn:hover {
                background: #2f855a;
                box-shadow: 0 4px 12px rgba(56, 161, 105, 0.2);
            }

            @media (max-width: 640px) {
                .eligibility-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="eligibility-modal-header">
            <h2 class="eligibility-modal-title">
                <i class="fas fa-check-circle"></i>
                {{ __('Donation Eligibility Requirements') }}
            </h2>
            <button @click="$dispatch('close-modal', '{{ $name }}')" class="eligibility-modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="eligibility-grid">
            <div class="eligibility-card">
                <div class="eligibility-card-icon"><i class="fas fa-calendar-alt"></i></div>
                <h4>{{ __('Age') }}</h4>
                <p>{{ __('You must be between :min and :max years old.', ['min' => $settings->min_donor_age, 'max' => $settings->max_donor_age]) }}
                </p>
            </div>
            <div class="eligibility-card">
                <div class="eligibility-card-icon"><i class="fas fa-weight"></i></div>
                <h4>{{ __('Weight') }}</h4>
                <p>{{ __('Your weight should not be less than :weight kg.', ['weight' => $settings->min_donor_weight]) }}
                </p>
            </div>
            <div class="eligibility-card">
                <div class="eligibility-card-icon"><i class="fas fa-heartbeat"></i></div>
                <h4>{{ __('General Health') }}</h4>
                <p>{{ __('The donor must not suffer from infectious or uncontrolled chronic diseases.') }}</p>
            </div>
            <div class="eligibility-card">
                <div class="eligibility-card-icon"><i class="fas fa-clock"></i></div>
                <h4>{{ __('Time Period') }}</h4>
                <p>{{ __('At least :days days must have passed since the last blood donation.', ['days' => $settings->min_days_between_donations]) }}
                </p>
            </div>
        </div>

        <div class="eligibility-notice">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>{{ __('Important Note:') }}</strong>
            {{ __('A rapid hemoglobin and blood pressure check will be performed before each donation to ensure your safety.') }}
        </div>

        <div class="eligibility-footer">
            <a href="{{ route('eligibility') }}" class="view-more-link">
                {{ __('Full Requirements and Medical Conditions') }}
                <i class="fas fa-arrow-left"></i>
            </a>
            <button @click="$dispatch('close-modal', '{{ $name }}')" class="close-btn">
                {{ __('OK') }}
            </button>
        </div>
    </div>
</x-modal>
