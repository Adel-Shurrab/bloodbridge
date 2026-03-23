<nav class="navbar" id="navbar" role="navigation" aria-label="{{ __('Main Navigation') }}">
    <div class="nav-container">
        <a href="{{ route('home') }}" class="logo"
            aria-label="{{ $settings->getTranslation('site_name') }} - {{ __('Homepage') }}">
            <img src="{{ $settings->site_logo ? Storage::url($settings->site_logo) : asset('assets/images/logo.png') }}"
                alt="{{ $settings->getTranslation('site_name') }} Logo" class="logo-icon" />
            <span>{{ $settings->getTranslation('site_name') }}</span>
        </a>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}" aria-current="page">{{ __('Home') }}</a></li>
            <li><a href="{{ route('home') }}#features">{{ __('Features') }}</a></li>
            <li><a href="{{ route('home') }}#how-it-works">{{ __('How it Works') }}</a></li>
            <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
            <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
        </ul>
        <div class="nav-buttons">
            @auth
                <a href="{{ auth()->user()->getDashboardUrl() }}" class="btn btn-primary">{{ __('Dashboard') }}</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="padding: 1rem 1.5rem;">{{ __('Log Out') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">{{ __('Log In') }}</a>
                <a href="{{ route('register.selection') }}" class="btn btn-primary">{{ __('Register') }}</a>
            @endauth

            <div class="lang-switcher">
                @if (app()->getLocale() === 'ar')
                    <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                        class="lang-link">
                        <span class="flag-icon">🇺🇸</span>
                        <span>English</span>
                    </a>
                @else
                    <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true) }}"
                        class="lang-link">
                        <span class="flag-icon">🇸🇦</span>
                        <span>العربية</span>
                    </a>
                @endif
            </div>
        </div>
        <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="{{ __('Open Menu') }}" aria-expanded="false"
            aria-controls="mobile-nav">☰</button>
    </div>
</nav>

<div class="mobile-nav" id="mobile-nav" aria-hidden="true">
    <button class="mobile-menu-close" id="mobile-menu-close" aria-label="{{ __('Close Menu') }}">×</button>
    <ul class="mobile-nav-links">
        <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li><a href="{{ route('home') }}#features">{{ __('Features') }}</a></li>
        <li><a href="{{ route('home') }}#how-it-works">{{ __('How it Works') }}</a></li>
        <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
        <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
    </ul>
    <div class="mobile-nav-buttons">
        @auth
            <a href="{{ auth()->user()->getDashboardUrl() }}" class="btn btn-primary">{{ __('Dashboard') }}</a>
            <form method="POST" action="{{ route('logout') }}" style="display: block; width: 100%;">
                @csrf
                <button type="submit" class="btn btn-outline" style="width: 100%; padding: 1rem 1.5rem;">{{ __('Log Out') }}</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline">{{ __('Log In') }}</a>
            <a href="{{ route('register.selection') }}" class="btn btn-primary">{{ __('Register') }}</a>
        @endauth

        <div class="mobile-lang-switcher" style="margin-top: 1.5rem; border-top: 1px solid #eee; padding-top: 1rem;">
            @if (app()->getLocale() === 'ar')
                <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                    class="lang-link"
                    style="display: flex; align-items: center; gap: 0.5rem; justify-content: center; color: #4b5563;">
                    <span class="flag-icon">🇺🇸</span>
                    <span>English</span>
                </a>
            @else
                <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true) }}"
                    class="lang-link"
                    style="display: flex; align-items: center; gap: 0.5rem; justify-content: center; color: #4b5563;">
                    <span class="flag-icon">🇸🇦</span>
                    <span>العربية</span>
                </a>
            @endif
        </div>
    </div>
</div>
