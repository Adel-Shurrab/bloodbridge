<x-layout title="من نحن - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/about.css') }}" />
    @endpush

    <section class="about-hero">
        <div class="about-hero-container">
            <div class="hero-badge">من نحن</div>
            <h1>حول جسر الدم</h1>
            <p>
                نحن منظمة غير ربحية ملتزمة بضمان توفير إمدادات دم آمنة وكافية للمرضى
                المحتاجين. رسالتنا هي ربط المتبرعين بالدم بسخاء بالمستفيدين، وتسهيل
                عمليات نقل الدم المنقذة للحياة، وتعزيز صحة المجتمع.
            </p>
        </div>
    </section>

    <section class="mission-vision">
        <div class="container-content">
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="card-icon mission-icon">🎯</div>
                    <h2>مهمتنا</h2>
                    <p>
                        في "جسر الدم"، نؤمن بأن لكل قطرة دم قيمتها. مهمتنا هي سد الفجوة
                        بين المتبرعين بالدم والمحتاجين إليه، وضمان عدم بقاء أي مريض دون دم
                        منقذ للحياة. نحن ملتزمون برفع مستوى الوعي، وتبسيط عملية التبرع،
                        وتعزيز ثقافة العطاء.
                    </p>
                </div>
                <div class="mission-card">
                    <div class="card-icon vision-icon">👁️</div>
                    <h2>رؤيتنا</h2>
                    <p>
                        نطمح إلى عالم لا يعاني فيه أي مريض من نقص الدم. رؤيتنا هي إنشاء
                        شبكة عالمية من المتبرعين الملتزمين والمؤسسات الصحية الموثوقة،
                        مدعومة بالتكنولوجيا الذكية التي تجعل التبرع بالدم سهلاً وفعالاً
                        ومؤثراً.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="values-section">
        <div class="container-content">
            <div class="section-header">
                <h2>قيمنا الأساسية</h2>
                <p>المبادئ التي توجه عملنا اليومي</p>
            </div>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">💎</div>
                    <h3>نزاهة</h3>
                    <p>
                        العمل بشفافية ومساءلة، وضمان أعلى معايير السلامة والأخلاق في كل
                        خطوة
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">❤️</div>
                    <h3>عطف</h3>
                    <p>
                        مدفوعًا بالتعاطف والالتزام العميق بمساعدة المرضى وعائلاتهم في
                        أوقاتهم الصعبة
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3>تعاون</h3>
                    <p>
                        العمل مع المستشفيات ومراكز الدم والشركاء المجتمعيين لتحقيق أقصى
                        قدر من التأثير
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">💡</div>
                    <h3>ابتكار</h3>
                    <p>
                        البحث المستمر عن طرق جديدة لتحسين تجربة التبرع والوصول إلى المزيد
                        من المتبرعين
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="container-content">
            <div class="section-header">
                <h2>فريق العمل</h2>
                <p>
                    نخبة من مهندسي أنظمة الحاسوب، يجمعهم شغف توظيف التكنولوجيا لخدمة الإنسانية
                    تحت إشراف أكاديمي متميز.
                </p>
            </div>

            <div class="team-grid" style="justify-content: center; margin-bottom: 2rem;">
                <div class="team-card">
                    <div class="team-image">
                        <div class="team-image-placeholder">👩‍🏫</div>
                    </div>
                    <h3>د. سهير حرب</h3>
                    <p class="team-role">المشرف الأكاديمي</p>
                    <p class="team-desc">
                        دكتوراه في هندسة أنظمة الحاسوب. قادت التوجيه الأكاديمي والإشراف العام لضمان جودة مخرجات مشروع
                        BloodBridge.
                    </p>
                </div>
            </div>

            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <div class="team-image-placeholder">👨‍💻</div>
                    </div>
                    <h3>عادل عبد الله شراب</h3>
                    <p class="team-role">مطور ومؤسس شريك</p>
                    <p class="team-desc">
                        مهندس أنظمة حاسوب، ساهم في هندسة النظام الخلفية وتطوير خوارزميات المطابقة الذكية.
                    </p>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <div class="team-image-placeholder">👨‍💻</div>
                    </div>
                    <h3>عبد الحليم أشرف حمدان</h3>
                    <p class="team-role">مطور ومؤسس شريك</p>
                    <p class="team-desc">
                        مهندس أنظمة حاسوب، متخصص في تطوير واجهات المستخدم وتجربة المستخدم (UI/UX) للمنصة.
                    </p>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <div class="team-image-placeholder">👨‍💻</div>
                    </div>
                    <h3>أنس فيصل أبو عمرة</h3>
                    <p class="team-role">مطور ومؤسس شريك</p>
                    <p class="team-desc">
                        مهندس أنظمة حاسوب، ركز على تكامل الخدمات (API Integration) وأنظمة الإشعارات الفورية.
                    </p>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <div class="team-image-placeholder">👨‍💻</div>
                    </div>
                    <h3>فيصل رامي الزير</h3>
                    <p class="team-role">مطور ومؤسس شريك</p>
                    <p class="team-desc">
                        مهندس أنظمة حاسوب، عمل على تأمين البيانات (Security) وإدارة قواعد البيانات المتقدمة.
                    </p>
                </div>
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
            <a href="{{ route('register') }}" class="btn btn-outline-white">سجل الآن</a>
        </div>
    </section>

    @push('scripts')
        <script src="{{ asset('assets/scripts/pages/about.js') }}"></script>
    @endpush
</x-layout>