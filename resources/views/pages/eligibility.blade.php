<x-layout title="{{ __('Eligibility Requirements') }} - {{ $settings->getTranslation('site_name') }}">
    <div class="eligibility-page-wrapper"
        style=" font-family: 'Cairo', sans-serif; background: #fdfdfd; padding-top: 5rem;">
        <!-- Hero Section -->
        <div class="eligibility-hero"
            style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); padding: 4rem 2rem; text-align: center; position: relative; overflow: hidden;">
            <div
                style="position: absolute; inset-block-start: -50px; inset-inline-start: -50px; width: 200px; height: 200px; background: rgba(211, 47, 47, 0.03); border-radius: 50%;">
            </div>
            <div style="max-width: 800px; margin: 0 auto; position: relative; z-index: 1;">
                <div
                    style="width: 70px; height: 70px; background: #fff; color: #d32f2f; border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1.5rem; box-shadow: 0 10px 20px rgba(211, 47, 47, 0.1);">
                    <i class="fas fa-notes-medical"></i>
                </div>
                <h1 style="font-size: 2.5rem; font-weight: 900; color: #1a202c; margin-bottom: 1rem;">
                    {{ __('Donation Eligibility Requirements') }}</h1>
                <p style="font-size: 1.1rem; color: #4a5568; line-height: 1.8;">
                    {{ __('Learn about health and medical criteria to ensure a safe donation experience for you and the recipients. We follow international protocols to ensure the highest standards of quality.') }}
                </p>
            </div>
        </div>

        <div class="container mx-auto" style="max-width: 1000px; padding: 4rem 1.5rem;">
            <div class="eligibility-notice-grid" style="display: grid; grid-template-columns: 240px 1fr; gap: 2rem">
                <!-- Sidebar Navigation (Desktop) -->
                <div class="hide">
                    <div
                        style="position: sticky; top: 100px; background: #fff; border: 1px solid #edf2f7; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                        <h3
                            style="font-size: 1.1rem; font-weight: 800; color: #d32f2f; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid #fff5f5;">
                            {{ __('Contents') }}</h3>
                        <ul
                            style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;">
                            <li><a href="#general"
                                    style="color: #4a5568; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;"><i
                                        class="fas fa-check-double" style="color: #38a169;"></i>
                                    {{ __('General Terms') }}</a></li>
                            <li><a href="#temporary"
                                    style="color: #4a5568; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;"><i
                                        class="fas fa-hourglass-half" style="color: #ed8936;"></i>
                                    {{ __('Temporary Deferrals') }}</a></li>
                            <li><a href="#permanent"
                                    style="color: #4a5568; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;"><i
                                        class="fas fa-ban" style="color: #e53e3e;"></i>
                                    {{ __('Permanent Deferrals') }}</a></li>
                            <li><a href="#pre-donation"
                                    style="color: #4a5568; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;"><i
                                        class="fas fa-utensils" style="color: #3182ce;"></i>
                                    {{ __('Before Donation') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="md:col-span-2">
                    <!-- General Section -->
                    <section id="general" style="margin-bottom: 4rem;">
                        <h2
                            style="font-size: 1.75rem; font-weight: 800; color: #1a202c; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 8px; height: 32px; background: #38a169; border-radius: 4px;"></span>
                            {{ __('General Donation Requirements') }}
                        </h2>
                        <div
                            style="background: #fff; border: 1px solid #edf2f7; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.02);">
                            <div style="display: grid; gap: 2rem;">
                                <div style="display: flex; gap: 1.25rem;">
                                    <div
                                        style="flex-shrink: 0; width: 48px; height: 48px; background: #f0fff4; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #38a169; font-size: 1.25rem;">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div>
                                        <h4
                                            style="font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem;">
                                            {{ __('Age and Weight') }}</h4>
                                        <p style="color: #718096; line-height: 1.7; font-size: 0.95rem;">
                                            {{ __('You must be between :min and :max years old, and your weight should not be less than :weight kg.', ['min' => $settings->min_donor_age, 'max' => $settings->max_donor_age, 'weight' => $settings->min_donor_weight]) }}
                                        </p>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 1.25rem;">
                                    <div
                                        style="flex-shrink: 0; width: 48px; height: 48px; background: #fdf2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #d32f2f; font-size: 1.25rem;">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div>
                                        <h4
                                            style="font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem;">
                                            {{ __('Vital Signs') }}</h4>
                                        <p style="color: #718096; line-height: 1.7; font-size: 0.95rem;">
                                            {{ __('The medical team will check blood pressure, temperature, and heart rate before donation.') }}
                                        </p>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 1.25rem;">
                                    <div
                                        style="flex-shrink: 0; width: 48px; height: 48px; background: #ebf8ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #3182ce; font-size: 1.25rem;">
                                        <i class="fas fa-tint"></i>
                                    </div>
                                    <div>
                                        <h4
                                            style="font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem;">
                                            {{ __('Hemoglobin Level') }}</h4>
                                        <p style="color: #718096; line-height: 1.7; font-size: 0.95rem;">
                                            {{ __('Hemoglobin levels must be within normal range (above 12.5 for women and 13.5 for men).') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Temporary Section -->
                    <section id="temporary" style="margin-bottom: 4rem;">
                        <h2
                            style="font-size: 1.75rem; font-weight: 800; color: #1a202c; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 8px; height: 32px; background: #ed8936; border-radius: 4px;"></span>
                            {{ __('Temporary Donation Deferrals') }}
                        </h2>
                        <div
                            style="background: #fffaf0; border: 1px solid #feebc8; border-radius: 20px; padding: 2rem;">
                            <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 1rem;">
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>{{ __('Receiving Vaccines') }}:</strong>
                                        {{ __('You may be required to wait two to four weeks depending on the type of vaccine.') }}</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>{{ __('Using Antibiotics') }}:</strong>
                                        {{ __('Wait 48-72 hours after ending the treatment.') }}</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>{{ __('Minor Surgery') }}:</strong>
                                        {{ __('Wait at least :days days depending on the surgery.', ['days' => $settings->min_days_after_surgery]) }}</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>{{ __('Pregnancy and Childbirth') }}:</strong>
                                        {{ __('Donation is prohibited during pregnancy and is allowed 6 months after delivery.') }}</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>{{ __('Previous Donation') }}:</strong>
                                        {{ __('You must wait :days days between consecutive donations.', ['days' => $settings->min_days_between_donations]) }}</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <!-- Permanent Section -->
                    <section id="permanent" style="margin-bottom: 4rem;">
                        <h2
                            style="font-size: 1.75rem; font-weight: 800; color: #1a202c; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 8px; height: 32px; background: #e53e3e; border-radius: 4px;"></span>
                            {{ __('Permanent Donation Deferrals') }}
                        </h2>
                        <div
                            style="background: #fff5f5; border: 1px solid #fed7d7; border-radius: 20px; padding: 2rem;">
                            <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 1rem;">
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-times-circle" style="color: #e53e3e; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #822727; line-height: 1.6;">{{ __('Chronic infectious diseases (such as Hepatitis B and C).') }}</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-times-circle" style="color: #e53e3e; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #822727; line-height: 1.6;">{{ __('Acute cardiovascular diseases.') }}</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-times-circle" style="color: #e53e3e; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #822727; line-height: 1.6;">{{ __('Cancers of all types (except for some cured skin cancers).') }}</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-times-circle" style="color: #e53e3e; margin-top: 5px;"></i>
                                    <span
                                        style="font-size: 0.95rem; color: #822727; line-height: 1.6;">{{ __('Diabetes that requires insulin (depending on local medical protocol).') }}</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <!-- Pre-donation Tip Section -->
                    <section id="pre-donation">
                        <div
                            style="background: linear-gradient(135deg, #d32f2f 0%, #a12323 100%); border-radius: 20px; padding: 2.5rem; color: white; box-shadow: 0 15px 30px rgba(211, 47, 47, 0.25);">
                            <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem;">
                                {{ __('Tips for Donation Day') }}
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-glass-whiskey"></i>
                                    <span>{{ __('Drink plenty of fluids') }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-bed"></i>
                                    <span>{{ __('Get enough sleep') }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-utensils"></i>
                                    <span>{{ __('Eat a healthy breakfast') }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-smoking-ban"></i>
                                    <span>{{ __('Avoid smoking before and after donation') }}</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <style>
        html {
            scroll-behavior: smooth;
        }

        @media (max-width: 768px) {
            .eligibility-hero h1 {
                font-size: 1.8rem !important;
            }

            .eligibility-notice-grid {
                grid-template-columns: 1fr !important;
            }

            .hide{
                display:none;
            }
        }
    </style>
</x-layout>
