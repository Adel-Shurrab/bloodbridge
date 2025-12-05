<x-layout title="الرئيسية - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/index.css') }}" />
    @endpush

    <section class="hero" id="home">
        <div class="hero-container">
            <div class="hero-content">
                <h1>
                    أعط الحياة<br />
                    <span class="highlight">قطرة قطرة</span>
                </h1>
                <p>
                    انضم إلى آلاف المتبرعين الذين ينقذون الأرواح يوميًا. نظام ذكي يربط
                    المتبرعين بالمحتاجين في الوقت المناسب.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap">
                    <a href="" class="btn btn-primary"
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
                <img src="{{ asset('assets/images/hero-image.jpg') }}" alt="Blood donation illustration - giving life drop by drop" class="hero-image-img" />
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <h2>لماذا التبرع؟</h2>
            <p>
                تبرعك هو شريان حياة في حالات الطوارئ ولمن يحتاجون إلى علاجات طويلة
                الأمد
            </p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>عملية آمنة وبسيطة</h3>
                <p>
                    تم تصميم عملية التبرع لدينا لضمان راحتك وسلامتك، وضمان تجربة سلسة من
                    البداية إلى النهاية
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>الجدولة في الوقت الفعلي</h3>
                <p>
                    يمكنك بسهولة جدولة مواعيد التبرع الخاصة بك في الوقت الفعلي، واختيار
                    الوقت والمكان الأنسب لك
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏥</div>
                <h3>موثوق به من قبل المستشفيات</h3>
                <p>
                    نتعاون مع المستشفيات الرائدة لضمان تأثير تبرعاتكم بشكل مباشر على
                    المحتاجين
                </p>
            </div>
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
        <a href="{{ route('') }}" class="btn">سجل الآن</a>
    </section>
</x-layout>