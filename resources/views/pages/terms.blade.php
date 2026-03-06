<x-layout title="شروط الخدمة - {{ $settings->site_name }}">

    <style>
        html {
            scroll-behavior: smooth;
        }

        .terms-hero {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 60%, #1a202c 100%);
            padding: 6rem 2rem 5rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .terms-hero::before {
            content: '';
            position: absolute;
            top: -80px;
            left: -80px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(211, 47, 47, 0.12) 0%, transparent 70%);
            border-radius: 50%;
        }

        .terms-hero::after {
            content: '';
            position: absolute;
            bottom: -60px;
            right: -60px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .terms-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(211, 47, 47, 0.2);
            border: 1px solid rgba(211, 47, 47, 0.4);
            color: #fc8181;
            padding: 0.4rem 1rem;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 1.5rem;
        }

        .terms-hero-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .terms-hero h1 {
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 1.25rem;
            line-height: 1.2;
        }

        .terms-hero p {
            font-size: 1.1rem;
            opacity: 0.8;
            line-height: 1.9;
            max-width: 650px;
            margin: 0 auto;
        }

        .terms-hero-meta {
            margin-top: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.06);
            padding: 0.5rem 1.25rem;
            border-radius: 100px;
            font-size: 0.85rem;
            opacity: 0.7;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Layout */
        .terms-layout {
            max-width: 1050px;
            margin: 0 auto;
            padding: 4rem 1.5rem;
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 3rem;
            direction: rtl;
        }

        /* Sidebar */
        .terms-sidebar {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .terms-sidebar-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .terms-sidebar-card h3 {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #a0aec0;
            margin-bottom: 1.25rem;
        }

        .terms-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 10px;
            text-decoration: none;
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        .terms-nav-item:hover {
            background: #f7fafc;
            color: #1a202c;
        }

        .terms-nav-num {
            width: 22px;
            height: 22px;
            background: #edf2f7;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #718096;
            flex-shrink: 0;
        }

        /* Content */
        .terms-content {
            min-width: 0;
        }

        .terms-section {
            margin-bottom: 3.5rem;
            scroll-margin-top: 100px;
        }

        .terms-section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .terms-section-num {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 900;
            flex-shrink: 0;
        }

        .terms-section-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #1a202c;
            margin: 0;
        }

        .terms-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 1.75rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        /* Danger card for disclaimer */
        .terms-card-danger {
            background: linear-gradient(135deg, #fff5f5 0%, #fffafa 100%);
            border-color: #fed7d7;
            border-right: 4px solid #e53e3e;
        }

        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 0.9rem;
        }

        .terms-list-item {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
            color: #4a5568;
            font-size: 0.97rem;
            line-height: 1.8;
        }

        .terms-list-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .terms-list-icon.green {
            background: #f0fff4;
            color: #38a169;
        }

        .terms-list-icon.red {
            background: #fff5f5;
            color: #e53e3e;
        }

        .terms-list-icon.blue {
            background: #ebf8ff;
            color: #3182ce;
        }

        .terms-list-icon.gray {
            background: #f7fafc;
            color: #718096;
        }

        .terms-inline-link {
            color: #d32f2f;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1px dashed #d32f2f;
            transition: all 0.2s;
        }

        .terms-inline-link:hover {
            color: #9b2c2c;
            border-bottom-style: solid;
        }

        .terms-info-box {
            background: #ebf8ff;
            border: 1px solid #bee3f8;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            color: #2c5282;
            font-size: 0.9rem;
            line-height: 1.7;
            margin-top: 1rem;
        }

        .terms-cta {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            text-align: center;
            color: white;
            margin-top: 1rem;
            box-shadow: 0 20px 40px rgba(26, 32, 44, 0.2);
        }

        .terms-cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #d32f2f;
            color: white;
            text-decoration: none;
            padding: 0.85rem 2.25rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 1.5rem;
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.35);
        }

        .terms-cta-btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4);
        }

        @media (max-width: 768px) {
            .terms-layout {
                grid-template-columns: 1fr;
            }

            .terms-sidebar {
                position: static;
                display: none;
            }

            .terms-hero h1 {
                font-size: 1.9rem;
            }

            .terms-hero {
                padding: 4rem 1.5rem 3.5rem;
            }

            .terms-card {
                padding: 1.25rem 1.25rem;
            }
        }
    </style>

    <!-- Hero -->
    <div class="terms-hero">
        <div style="position: relative; z-index: 1;">
            <div class="terms-hero-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>شروط الخدمة والاستخدام</h1>
            <p>يرجى قراءة هذه الشروط بعناية قبل استخدام منصة {{ $settings->site_name }}. باستخدامك للمنصة، فإنك توافق
                على الالتزام الكامل بهذه الشروط والأحكام.</p>
            <div class="terms-hero-meta">
                <i class="fas fa-calendar-alt"></i>
                آخر تحديث: {{ date('d/m/Y') }}
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div style="background: #f7fafc; padding-bottom: 4rem;">
        <div class="terms-layout">

            <!-- Sidebar -->
            <aside class="terms-sidebar">
                <div class="terms-sidebar-card">
                    <h3>فهرس المحتوى</h3>
                    <nav>
                        <a href="#disclaimer" class="terms-nav-item">
                            <span class="terms-nav-num" style="background:#fff5f5; color:#e53e3e;">١</span>
                            إخلاء المسؤولية الطبية
                        </a>
                        <a href="#service-nature" class="terms-nav-item">
                            <span class="terms-nav-num">٢</span>
                            طبيعة الخدمة
                        </a>
                        <a href="#donor-duties" class="terms-nav-item">
                            <span class="terms-nav-num">٣</span>
                            التزامات المتبرع
                        </a>
                        <a href="#org-duties" class="terms-nav-item">
                            <span class="terms-nav-num">٤</span>
                            التزامات المؤسسات
                        </a>
                        <a href="#privacy" class="terms-nav-item">
                            <span class="terms-nav-num" style="background:#ebf8ff; color:#3182ce;">٥</span>
                            الخصوصية والبيانات
                        </a>
                        <a href="#account" class="terms-nav-item">
                            <span class="terms-nav-num">٦</span>
                            تعليق الحساب
                        </a>
                        <a href="#updates" class="terms-nav-item">
                            <span class="terms-nav-num">٧</span>
                            تعديل الشروط
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Content -->
            <main class="terms-content">

                {{-- 1. Medical Disclaimer --}}
                <section id="disclaimer" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#fff5f5; color:#e53e3e;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h2 class="terms-section-title">إخلاء المسؤولية الطبية</h2>
                    </div>
                    <div class="terms-card terms-card-danger">
                        <p
                            style="font-size:0.9rem; font-weight:700; color:#c53030; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem;">
                            <i class="fas fa-exclamation-circle"></i> هذا البند هو الأهم — يرجى قراءته بعناية
                        </p>
                        <ul class="terms-list">
                            <li class="terms-list-item">
                                <span class="terms-list-icon red"><i class="fas fa-times"></i></span>
                                <span>منصة <strong>{{ $settings->site_name }}</strong> هي وسيط تقني فقط يهدف إلى تسهيل
                                    التواصل بين المتبرعين والمستشفيات أو بنوك الدم. <strong>المنصة لا تقدم أي استشارات
                                        طبية</strong> بأي شكل من الأشكال.</span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon red"><i class="fas fa-times"></i></span>
                                <span>المستشفى أو بنك الدم المتلقي هو <strong>المسؤول الوحيد والمباشر</strong> عن الفحص
                                    الطبي للمتبرع، وتقييم أهليته النهائية، وإجراء فحوصات السلامة الكاملة على
                                    الدم.</span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon red"><i class="fas fa-times"></i></span>
                                <span>المنصة وفريقها <strong>غير مسؤولين</strong> عن أي مضاعفات صحية تطرأ على المتبرع
                                    أثناء العملية أو بعدها، أو عن أي نتائج طبية سلبية للمريض المتلقي.</span>
                            </li>
                        </ul>
                    </div>
                </section>

                {{-- 2. Service Nature --}}
                <section id="service-nature" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#f0fff4; color:#38a169;">٢</div>
                        <h2 class="terms-section-title">طبيعة الخدمة</h2>
                    </div>
                    <div class="terms-card">
                        <p style="color:#4a5568; line-height:1.9; margin:0;">
                            تقدم منصة <strong>{{ $settings->site_name }}</strong> خدمة تقنية لإنشاء قاعدة بيانات حية
                            وفاعلة للمتبرعين بالدم، وإتاحة الفرصة للمؤسسات الصحية المعتمدة لإرسال طلبات تبرع موجهة عند
                            الحاجة الطارئة. الخدمات الأساسية للمنصة <strong>مجانية بالكامل</strong> للمتبرعين الأفراد.
                        </p>
                        <div class="terms-info-box">
                            <i class="fas fa-info-circle" style="margin-top:3px; flex-shrink:0;"></i>
                            <span>المنصة لا تضمن توفر متبرعين في أي وقت، ولا تتحمل مسؤولية أي تأخير في الاستجابة لطلبات
                                التبرع في حالات الطوارئ.</span>
                        </div>
                    </div>
                </section>

                {{-- 3. Donor Duties --}}
                <section id="donor-duties" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#ebf8ff; color:#3182ce;">٣</div>
                        <h2 class="terms-section-title">التزامات المتبرع</h2>
                    </div>
                    <div class="terms-card">
                        <ul class="terms-list">
                            <li class="terms-list-item">
                                <span class="terms-list-icon green"><i class="fas fa-check"></i></span>
                                <span><strong>دقة المعلومات:</strong> تقر بأن جميع البيانات الصحية والشخصية التي أدخلتها
                                    (كفصيلة الدم والأمراض والعمر) دقيقة وحديثة ولا تحتوي على معلومات مضللة.</span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon green"><i class="fas fa-check"></i></span>
                                <span><strong>الموافقة على الإشعارات:</strong> بالتسجيل، توافق على تلقي إشعارات عبر
                                    البريد الإلكتروني أو الرسائل النصية عند مطابقة طلب تبرع عاجل لفصيلة دمك.</span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon green"><i class="fas fa-check"></i></span>
                                <span><strong>عدم الإلزام:</strong> تلقي طلب التبرع لا يلزمك قانونياً بالتبرع؛ لك كامل
                                    الحرية في القبول أو الرفض بناءً على ظروفك الصحية والشخصية في تلك اللحظة.</span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon green"><i class="fas fa-check"></i></span>
                                <span><strong>الصدق الطبي الكامل:</strong> أنت ملتزم بالإفصاح الكامل عن أي موانع تبرع أو
                                    تاريخ مرضي للطاقم الطبي في المستشفى <em>قبل</em> التبرع الفعلي، بغض النظر عما هو
                                    مسجل في النظام.</span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon blue"><i class="fas fa-info"></i></span>
                                <span>عمر التسجيل المسموح به: بين <strong>{{ $settings->min_donor_age }}</strong> و
                                    <strong>{{ $settings->max_donor_age }}</strong> عاماً. الحد الأدنى للوزن:
                                    <strong>{{ $settings->min_donor_weight }} كجم</strong>.</span>
                            </li>
                        </ul>
                    </div>
                </section>

                {{-- 4. Organization Duties --}}
                <section id="org-duties" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#fefcbf; color:#d69e2e;">٤</div>
                        <h2 class="terms-section-title">التزامات المؤسسات الصحية</h2>
                    </div>
                    <div class="terms-card">
                        <ul class="terms-list">
                            <li class="terms-list-item">
                                <span class="terms-list-icon gray"><i class="fas fa-check"></i></span>
                                <span>يقتصر استخدام النظام على البحث عن متبرعين للحالات الطبية الحقيقية والطارئة، ويُحظر
                                    تماماً استغلال بيانات المتبرعين لأي غرض تجاري أو إعلاني.</span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon gray"><i class="fas fa-check"></i></span>
                                <span>النظام يقيّد عدد الطلبات اليومية (الحد الأقصى
                                    <strong>{{ $settings->org_max_requests_per_day }}</strong> طلبات يومياً). يُمنع
                                    الإرسال العشوائي لتجنب إزعاج المتبرعين.</span>
                            </li>
                            <li class="terms-list-item">
                                <span class="terms-list-icon gray"><i class="fas fa-check"></i></span>
                                <span>كل مؤسسة تتحمل المسؤولية الكاملة والمنفردة عن تطبيق معايير السلامة وإجراء الفحوصات
                                    المخبرية والفيروسية على أي دم يُجمع عبر المنصة.</span>
                            </li>
                        </ul>
                    </div>
                </section>

                {{-- 5. Privacy --}}
                <section id="privacy" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#ebf8ff; color:#3182ce;">٥</div>
                        <h2 class="terms-section-title">الخصوصية والبيانات</h2>
                    </div>
                    <div class="terms-card">
                        <p style="color:#4a5568; line-height:1.9; margin:0;">
                            نحن نشارك الحد الأدنى من بيانات الاتصال الضرورية (كرقم الهاتف وفصيلة الدم) مع المستشفى
                            <strong>فقط</strong> عند مطابقة طلب تبرع، وبموافقتك الضمنية عند التسجيل. لمزيد من التفاصيل،
                            اطلع على <a href="javascript:void(0)"
                                @click.prevent="$dispatch('open-modal', 'privacyModal')"
                                class="terms-inline-link">سياسة الخصوصية الكاملة</a>.
                        </p>
                    </div>
                </section>

                {{-- 6. Account --}}
                <section id="account" class="terms-section">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#fff5f5; color:#e53e3e;">٦</div>
                        <h2 class="terms-section-title">تعليق أو إنهاء الحساب</h2>
                    </div>
                    <div class="terms-card">
                        <p style="color:#4a5568; line-height:1.9; margin:0;">
                            نحتفظ بالحق في تعليق أو حذف أي حساب يثبت استخدامه للمنصة بطريقة مسيئة، بما يشمل: إدخال
                            بيانات كاذبة، التخلف المتكرر عن التبرع بعد التأكيد، أو أي انتهاك لشروط الخصوصية. سيتم إبلاغ
                            أصحاب الحسابات قبل أي إجراء حيثما أمكن.
                        </p>
                    </div>
                </section>

                {{-- 7. Updates --}}
                <section id="updates" class="terms-section" style="margin-bottom: 0;">
                    <div class="terms-section-header">
                        <div class="terms-section-num" style="background:#f7fafc; color:#718096;">٧</div>
                        <h2 class="terms-section-title">التعديلات على الشروط</h2>
                    </div>
                    <div class="terms-card">
                        <p style="color:#4a5568; line-height:1.9; margin:0;">
                            للمنصة الحق في تحديث هذه الشروط في أي وقت. سيتم إشعار المستخدمين بأي تغييرات جوهرية عبر
                            المنصة أو البريد الإلكتروني. استمرار استخدامك للموقع بعد أي تعديل يُعدّ قبولاً صريحاً
                            للتغييرات.
                        </p>
                    </div>
                </section>

                <!-- CTA Footer -->
                <div class="terms-cta" style="margin-top: 3.5rem;">
                    <div style="font-size:2rem; margin-bottom:1rem;">💬</div>
                    <h3 style="font-size:1.35rem; font-weight:800; margin-bottom:0.5rem;">هل لديك استفسار قانوني؟</h3>
                    <p style="opacity:0.75; font-size:0.95rem;">فريق {{ $settings->site_name }} مستعد للإجابة على أي
                        تساؤلات تتعلق بهذه الشروط.</p>
                    <a href="{{ route('contact') }}" class="terms-cta-btn">
                        <i class="fas fa-envelope"></i>
                        تواصل مع الإدارة
                    </a>
                </div>

            </main>
        </div>
    </div>

</x-layout>
