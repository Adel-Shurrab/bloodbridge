<x-layout title="الرئيسية - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/index.css') }}" />
    @endpush

    <section class="hero" id="home">
        <div style="background: yellow; color: black; padding: 10px; text-align: center;">
            DEBUG: {{ $settings->home_hero_title }}
        </div>
        <div class="hero-container">
            <div class="hero-content">
                <h1>{{ $settings->home_hero_title }}</h1>
                <p>{{ $settings->home_hero_subtitle }}</p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap">
                    <a href="{{ route('register.donor') }}" class="btn btn-primary"
                        style="font-size: 1.1rem; padding: 1rem 2.5rem">تبرع الآن</a>
                    <a href="#how-it-works" class="btn btn-outline">اقرأ المزيد</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-card">
                        <span class="stat-number">5000+</span>
                        <span class="stat-label">تبرعات مكتملة</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">120</span>
                        <span class="stat-label">مستشفى شريك</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">4500</span>
                        <span class="stat-label">أرواح منقذة</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ $settings->home_hero_image ? Storage::url($settings->home_hero_image) : asset('assets/images/hero-image.jpg') }}"
                    alt="{{ $settings->home_hero_title }}" class="hero-image-img" />
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <h2>{{ $settings->home_features_title }}</h2>
            <p>{{ $settings->home_features_subtitle }}</p>
        </div>
        <div class="features-grid">
            @foreach($settings->home_features ?? [] as $feature)
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
            <h2>كيف يعمل؟</h2>
        </div>
        <div class="tabs">
            <button class="tab-btn active" data-tab="donor">كمتبرع</button>
            <button class="tab-btn" data-tab="org">كمنظمة</button>
        </div>
        <div class="tab-content-wrapper">
            <div class="steps-grid active" id="donor-steps">
                @foreach($settings->home_how_it_works_donor??[] as $index => $step)
                    <div class="step-card">
                        <div class="step-number">{{ $index + 1 }}</div>
                        <h3>{{ $step['title'] ?? '' }}</h3>
                        <p>{{ $step['text'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>

            <div class="steps-grid" id="org-steps">
                @foreach($settings->home_how_it_works_org ?? [] as $index => $step)
                    <div class="step-card">
                        <div class="step-number">{{ $index + 1 }}</div>
                        <h3>{{ $step['title'] ?? '' }}</h3>
                        <p>{{ $step['text'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>{{ $settings->home_cta_title }}</h2>
        <p>{{ $settings->home_cta_subtitle }}</p>
        <a href="{{ route('register.selection') }}" class="btn">سجل الآن</a>
    </section>
</x-layout>