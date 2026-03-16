<x-layout title="{{ __('Home') }} - {{ $settings->getTranslation('site_name') }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/index.css') }}" />
    @endpush

    <section class="hero" id="home">
        <div class="hero-container">
            <div class="hero-content">
                <h1>{{ $settings->getTranslation('home_hero_title') }}</h1>
                <p>{{ $settings->getTranslation('home_hero_subtitle') }}</p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap">
                    <a href="{{ route('register.donor') }}" class="btn btn-primary"
                        style="font-size: 1.1rem; padding: 1rem 2.5rem">{{ __('Donate Now') }}</a>
                    <a href="#how-it-works" class="btn btn-outline">{{ __('Read More') }}</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-card">
                        <span class="stat-number">{{ $stats['donations_count'] }}</span>
                        <span class="stat-label">{{ __('Completed Donations') }}</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $stats['orgs_count'] }}</span>
                        <span class="stat-label">{{ __('Partner Hospital') }}</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">{{ $stats['lives_saved'] }}</span>
                        <span class="stat-label">{{ __('Lives Saved') }}</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ $settings->home_hero_image ? Storage::url($settings->home_hero_image) : asset('assets/images/hero-image.jpg') }}"
                    alt="{{ $settings->getTranslation('home_hero_title') }}" class="hero-image-img" />
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <h2>{{ $settings->getTranslation('home_features_title') }}</h2>
            <p>{{ $settings->getTranslation('home_features_subtitle') }}</p>
        </div>
        <div class="features-grid">
            @foreach ($settings->getTranslation('home_features') ?? [] as $feature)
                <div class="feature-card">
                    <div class="feature-icon">{{ $feature['icon'] ?? '📁' }}</div>
                    <h3>{{ $feature['title'] ?? '' }}</h3>
                    <p>{{ $feature['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="how-it-works" id="how-it-works">
        <div class="section-header">
            <h2>{{ __('How it Works?') }}</h2>
        </div>
        <div class="tabs">
            <button class="tab-btn active" data-tab="donor">{{ __('As a Donor') }}</button>
            <button class="tab-btn" data-tab="org">{{ __('As an Organization') }}</button>
        </div>
        <div class="tab-content-wrapper">
            <div class="steps-grid active" id="donor-steps">
                @foreach ($settings->getTranslation('home_how_it_works_donor') ?? [] as $step)
                    <div class="step-card">
                        <div class="step-number">{{ $loop->iteration }}</div>
                        <h3>{{ $step['title'] ?? '' }}</h3>
                        <p>{{ $step['text'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>

            <div class="steps-grid" id="org-steps">
                @foreach ($settings->getTranslation('home_how_it_works_org') ?? [] as $step)
                    <div class="step-card">
                        <div class="step-number">{{ $loop->iteration }}</div>
                        <h3>{{ $step['title'] ?? '' }}</h3>
                        <p>{{ $step['text'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>{{ $settings->getTranslation('home_cta_title') }}</h2>
        <p>{{ $settings->getTranslation('home_cta_subtitle') }}</p>
        <a href="{{ route('register.selection') }}" class="btn">{{ __('Register Now') }}</a>
    </section>
</x-layout>
