<x-layout title="الرئيسية - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/index.css') }}" />
    @endpush

    <section class="hero" id="home">
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
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>تسجيل الدخول</h3>
                    <p>أنشئ ملفك الشخصي وانضم إلى مجتمعنا من المنقذين</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>قبول الطلبات</h3>
                    <p>استقبل طلبات التبرع من المنظمات بناءً على فصيلة دمك وموقعك</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>التبرع</h3>
                    <p>تفضل بزيارة المركز المخصص للتبرع وإنقاذ الأرواح</p>
                </div>
            </div>

            <div class="steps-grid" id="org-steps">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>تسجيل المنظمة</h3>
                    <p>سجل منظمتك وأكمل الوثائق المطلوبة للتحقق</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>إنشاء طلبات التبرع</h3>
                    <p>حدد احتياجاتك من فصائل الدم وأنشئ طلبات التبرع</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>إدارة التبرعات</h3>
                    <p>تواصل مع المتبرعين وأدر عمليات التبرع بكفاءة</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>ابدأ رحلة الإنقاذ اليوم</h2>
        <p>كل تبرع يمكن أن ينقذ حياة ثلاثة أشخاص. انضم إلينا الآن وكن بطلاً</p>
        <a href="{{ route('register.selection') }}" class="btn">سجل الآن</a>
    </section>
</x-layout>