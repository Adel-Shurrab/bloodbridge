<x-layout title="{{ __('Contact Us') }} - {{ $settings->getTranslation('site_name') }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/contact.css') }}" />
        <script>
            window.translations = {
                'Name min 3 chars': '{{ __('Name must be at least 3 characters') }}',
                'Invalid email': '{{ __('Please enter a valid email address') }}',
                'Subject min 5 chars': '{{ __('Subject must be at least 5 characters') }}',
                'Message min 10 chars': '{{ __('Message must be at least 10 characters') }}',
                'Privacy agreement required': '{{ __('You must agree to the privacy policy') }}',
                'Success message': '{{ __('Your message has been sent successfully! We will get back to you soon.') }}',
                'Error sending message': '{{ __('An error occurred while sending your message. Please try again later.') }}'
            };

            function __(key, replace = {}) {
                let translation = window.translations[key] || key;
                for (let placeholder in replace) {
                    translation = translation.replace(':' + placeholder, replace[placeholder]);
                }
                return translation;
            }
        </script>
    @endpush

    <main class="contact-page">
        <section class="contact-header">
            <div class="section-header" style="text-align: center">
                <h2>{{ $settings->getTranslation('contact_hero_title') }}</h2>
                <p>{{ $settings->getTranslation('contact_hero_subtitle') }}</p>
            </div>
        </section>

        <section class="contact-content">
            <div class="contact-grid">
                <div class="form-card">
                    @if ($settings->enable_contact_messages)
                        <h3>{{ __('Send us a Message') }}</h3>
                        <form id="contactForm" method="POST" action="{{ route('contact.submit') }}"
                            class="contact-form">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{ __('Your Name') }} <span class="required">*</span></label>
                                <input type="text" id="name" name="name"
                                    placeholder="{{ __('Enter your name') }}" required aria-required="true"
                                    aria-describedby="nameError" />
                                <span class="error-message" id="nameError"></span>
                            </div>
                            <div class="form-group">
                                <label for="email">{{ __('Your Email') }} <span class="required">*</span></label>
                                <input type="email" id="email" name="email" placeholder="name@example.com"
                                    required aria-required="true" aria-describedby="emailError" />
                                <span class="error-message" id="emailError"></span>
                            </div>
                            <div class="form-group">
                                <label for="subject">{{ __('Subject') }} <span class="required">*</span></label>
                                <input type="text" id="subject" name="subject"
                                    placeholder="{{ __('How can we help you?') }}" required aria-required="true"
                                    aria-describedby="subjectError" />
                                <span class="error-message" id="subjectError"></span>
                            </div>
                            <div class="form-group">
                                <label for="message">{{ __('Message') }} <span class="required">*</span></label>
                                <textarea id="message" name="message" rows="5" placeholder="{{ __('Write your message here...') }}" required
                                    aria-required="true" aria-describedby="messageError"></textarea>
                                <span class="error-message" id="messageError"></span>
                            </div>
                            <div class="form-group checkbox">
                                <input type="checkbox" id="privacy" name="privacy" required aria-required="true" />
                                <label for="privacy">{{ __('I agree to the') }} <a href="javascript:void(0)"
                                        @click.prevent="$dispatch('open-modal', 'privacyModal')">{{ __('Privacy Policy') }}</a>
                                    <span class="required">*</span></label>
                            </div>
                            <button type="submit" class="btn btn-primary full-width" id="submitBtn">
                                <span class="btn-text">{{ __('Send Message') }}</span>
                                <span class="btn-loader" style="display: none;">{{ __('Sending...') }}</span>
                            </button>
                            <div class="form-message success-message" id="successMessage" style="display: none;"></div>
                            <div class="form-message error-message" id="errorMessage" style="display: none;"></div>
                        </form>
                    @else
                        <div class="text-center" style="padding: 40px 20px;">
                            <i class="fa-solid fa-envelope-circle-xmark"
                                style="font-size: 4rem; color: #9ca3af; margin-bottom: 20px;"></i>
                            <h3>{{ __('Sorry, receiving messages is currently disabled') }}</h3>
                            <p style="color: #6b7280; margin-top: 10px;">
                                {{ __('You can contact us via the other contact channels shown below.') }}</p>
                        </div>
                    @endif
                </div>

                <div class="info-card">
                    <div class="info-section">
                        <h3>{{ __('Contact Information') }}</h3>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="info-text">
                                <h4>{{ __('Phone') }}</h4>
                                <a
                                    href="tel:{{ str_replace(['-', ' ', '(', ')'], '', $settings->support_phone) }}">{{ $settings->support_phone }}</a>
                                <p>{{ $settings->getTranslation('working_days') }}، {{ $settings->getTranslation('working_hours') }}</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div class="info-text">
                                <h4>{{ __('Email') }}</h4>
                                <a href="mailto:{{ $settings->support_email }}">{{ $settings->support_email }}</a>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="info-text">
                                <h4>{{ __('Address') }}</h4>
                                <p>{{ $settings->getTranslation('address') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-section" id="faq" style="scroll-margin-top: 100px;">
                        <h3>{{ __('Frequently Asked Questions') }}</h3>
                        @foreach ($settings->getTranslation('contact_faqs') ?? [] as $faq)
                            <div class="faq-item">
                                <button class="faq-question">
                                    <span>{{ $faq['question'] ?? '' }}</span>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <div class="faq-answer">
                                    <p>{{ $faq['answer'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    @push('scripts')
        <script src="{{ asset('assets/scripts/pages/contact.js') }}"></script>
    @endpush
</x-layout>
