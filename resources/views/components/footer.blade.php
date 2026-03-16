<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-section">
                <h4>{{ __('About :site', ['site' => $settings->getTranslation('site_name')]) }}</h4>
                <p>{{ $settings->getTranslation('seo_description') }}</p>
                <div class="social-links">
                    @if ($settings->facebook_url)
                        <a href="{{ $settings->facebook_url }}" aria-label="{{ __('Facebook') }}"
                            title="{{ __('Follow us on Facebook') }}"><i class="fab fa-facebook"></i></a>
                    @endif
                    @if ($settings->twitter_url)
                        <a href="{{ $settings->twitter_url }}" aria-label="{{ __('Twitter') }}"
                            title="{{ __('Follow us on Twitter') }}"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if ($settings->instagram_url)
                        <a href="{{ $settings->instagram_url }}" aria-label="{{ __('Instagram') }}"
                            title="{{ __('Follow us on Instagram') }}"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if ($settings->linkedin_url)
                        <a href="{{ $settings->linkedin_url }}" aria-label="{{ __('LinkedIn') }}"
                            title="{{ __('Follow us on LinkedIn') }}"><i class="fab fa-linkedin"></i></a>
                    @endif
                </div>
            </div>

            <div class="footer-section">
                <h4>{{ __('Quick Links') }}</h4>
                <ul>
                    <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
                    <li><a href="javascript:void(0)"
                            @click.prevent="$dispatch('open-modal', 'privacyModal')">{{ __('Privacy Policy') }}</a>
                    </li>
                    <li><a href="{{ route('terms') }}">{{ __('Terms of Service') }}</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>{{ __('For Donors') }}</h4>
                <ul>
                    <li><a href="{{ route('register.donor') }}">{{ __('Register as a Donor') }}</a></li>
                    <li><a href="javascript:void(0)"
                            @click.prevent="$dispatch('open-modal', 'eligibilityModal')">{{ __('Eligibility Requirements') }}</a>
                    </li>
                    <li><a href="{{ route('contact') }}#faq">{{ __('Frequently Asked Questions') }}</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>{{ __('For Organizations') }}</h4>
                <ul>
                    <li><a href="{{ route('register.organization') }}">{{ __('Register as an Organization') }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ $settings->getTranslation('site_name') }}.
                {{ __('All rights reserved.') }}</p>
            <p>{{ $settings->getTranslation('site_slogan') }} 🩸</p>
        </div>
    </div>
</footer>
