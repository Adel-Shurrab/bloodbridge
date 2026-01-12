<x-layout title="من نحن - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/about.css') }}" />
    @endpush

    <section class="about-hero">
        <div class="about-hero-container">
            <div class="hero-badge">من نحن</div>
            <h1>{{ $settings->about_hero_title }}</h1>
            <p>{{ $settings->about_hero_subtitle }}</p>
        </div>
    </section>

    <section class="mission-vision">
        <div class="container-content">
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="card-icon mission-icon">🎯</div>
                    <h2>{{ $settings->about_mission_title1 }}</h2>
                    <p>{{ $settings->about_mission_text1 }}</p>
                    @if($settings->about_mission_image1)
                        <img src="{{ Storage::url($settings->about_mission_image1) }}"
                            alt="{{ $settings->about_mission_title1 }}"
                            style="max-width: 100%; border-radius: 10px; margin-top: 1rem;">
                    @endif
                </div>
                <div class="mission-card">
                    <div class="card-icon vision-icon">👁️</div>
                    <h2>{{ $settings->about_mission_title2 }}</h2>
                    <p>{{ $settings->about_mission_text2 }}</p>
                    @if($settings->about_mission_image2)
                        <img src="{{ Storage::url($settings->about_mission_image2) }}"
                            alt="{{ $settings->about_mission_title2 }}"
                            style="max-width: 100%; border-radius: 10px; margin-top: 1rem;">
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="values-section">
        <div class="container-content">
            <div class="section-header">
                <h2>{{ $settings->about_values_title }}</h2>
                <p>{{ $settings->about_values_subtitle }}</p>
            </div>
            <div class="values-grid">
                @foreach($settings->about_values ?? [] as $value)
                    <div class="value-card">
                        <div class="value-icon">
                            @if($value['image'] ?? null)
                                <img src="{{ Storage::url($value['image']) }}" alt="{{ $value['title'] }}" style="width: 40px; height: 40px; object-fit: contain;">
                            @else
                                💎
                            @endif
                        </div>
                        <h3>{{ $value['title'] ?? '' }}</h3>
                        <p>{{ $value['text'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container-content">
            <div class="section-header">
                <h2>{{ $settings->about_team_title }}</h2>
                <p>{{ $settings->about_team_subtitle }}</p>
            </div>

            <div class="team-grid">
                @foreach($settings->about_team_members ?? [] as $member)
                    <div class="team-card">
                        <div class="team-image">
                            @if($member['image'] ?? null)
                                <img src="{{ Storage::url($member['image']) }}" alt="{{ $member['name'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                <div class="team-image-placeholder">👤</div>
                            @endif
                        </div>
                        <h3>{{ $member['name'] ?? '' }}</h3>
                        <p class="team-role">{{ $member['role'] ?? '' }}</p>
                        <p class="team-desc">{{ $member['bio'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="impact-section">
        <div class="container-content">
            <div class="section-header">
                <h2>تأثيرنا</h2>
                <p>منذ انطلاقتنا، ساهم جسر الدم في تسهيل آلاف التبرعات بالدم</p>
            </div>
            <div class="impact-stats">
                <div class="impact-card">
                    <div class="impact-number" data-target="10000">0</div>
                    <p>تبرعات مكتملة</p>
                </div>
                <div class="impact-card">
                    <div class="impact-number" data-target="50">0</div>
                    <p>مستشفى شريك</p>
                </div>
                <div class="impact-card">
                    <div class="impact-number" data-target="30000">0</div>
                    <p>أرواح منقذة</p>
                </div>
                <div class="impact-card">
                    <div class="impact-number" data-target="5000">0</div>
                    <p>متبرع نشط</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>انضم إلى رحلتنا</h2>
        <p>
            كن جزءًا من مجتمعنا وساعد في إنقاذ الأرواح. سواء كنت متبرعًا أو منظمة،
            نحن نرحب بك
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('contact') }}" class="btn">تواصل معنا</a>
            <a href="{{ route('register.selection') }}" class="btn btn-outline-white">سجل الآن</a>
        </div>
    </section>

    @push('scripts')
        <script src="{{ asset('assets/scripts/pages/about.js') }}"></script>
    @endpush
</x-layout>