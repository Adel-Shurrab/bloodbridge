<x-layout title="متطلبات الأهلية - {{ $settings->site_name }}">
    <div class="eligibility-page-wrapper"
        style="direction: rtl; font-family: 'Cairo', sans-serif; background: #fdfdfd; padding-top: 5rem;">
        <!-- Hero Section -->
        <div class="eligibility-hero"
            style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); padding: 4rem 2rem; text-align: center; position: relative; overflow: hidden;">
            <div
                style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(211, 47, 47, 0.03); border-radius: 50%;">
            </div>
            <div style="max-width: 800px; margin: 0 auto; position: relative; z-index: 1;">
                <div
                    style="width: 70px; height: 70px; background: #fff; color: #d32f2f; border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1.5rem; box-shadow: 0 10px 20px rgba(211, 47, 47, 0.1);">
                    <i class="fas fa-notes-medical"></i>
                </div>
                <h1 style="font-size: 2.5rem; font-weight: 900; color: #1a202c; margin-bottom: 1rem;">متطلبات الأهلية
                    للتبرع
                </h1>
                <p style="font-size: 1.1rem; color: #4a5568; line-height: 1.8;">تعرف على المعايير الصحية والطبية لضمان
                    تجربة
                    تبرع آمنة لك وللمستفيدين. نحن نتبع بروتوكولات عالمية لضمان أعلى معايير الجودة.</p>
            </div>
        </div>

        <div class="container mx-auto" style="max-width: 1000px; padding: 4rem 1.5rem;">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Sidebar Navigation (Desktop) -->
                <div class="hidden md:block">
                    <div
                        style="position: sticky; top: 100px; background: #fff; border: 1px solid #edf2f7; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                        <h3
                            style="font-size: 1.1rem; font-weight: 800; color: #d32f2f; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid #fff5f5;">
                            المحتويات</h3>
                        <ul
                            style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;">
                            <li><a href="#general"
                                    style="color: #4a5568; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;"><i
                                        class="fas fa-check-double" style="color: #38a169;"></i> شروط عامة</a></li>
                            <li><a href="#temporary"
                                    style="color: #4a5568; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;"><i
                                        class="fas fa-hourglass-half" style="color: #ed8936;"></i> موانع مؤقتة</a></li>
                            <li><a href="#permanent"
                                    style="color: #4a5568; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;"><i
                                        class="fas fa-ban" style="color: #e53e3e;"></i> موانع دائمة</a></li>
                            <li><a href="#pre-donation"
                                    style="color: #4a5568; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;"><i
                                        class="fas fa-utensils" style="color: #3182ce;"></i> قبل التبرع</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="md:col-span-2">
                    <!-- General Section -->
                    <section id="general" style="margin-bottom: 4rem;">
                        <h2
                            style="font-size: 1.75rem; font-weight: 800; color: #1a202c; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 8px; height: 32px; background: #38a169; border-radius: 4px;"></span>
                            الشروط العامة للتبرع
                        </h2>
                        <div
                            style="background: #fff; border: 1px solid #edf2f7; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.02);">
                            <div style="display: grid; gap: 2rem;">
                                <div style="display: flex; gap: 1.25rem;">
                                    <div
                                        style="flex-shrink: 0; width: 48px; height: 48px; background: #f0fff4; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #38a169; font-size: 1.25rem;">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div>
                                        <h4
                                            style="font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem;">
                                            العمر والوزن</h4>
                                        <p style="color: #718096; line-height: 1.7; font-size: 0.95rem;">يجب أن يكون
                                            عمرك
                                            بين {{ $settings->min_donor_age }} و {{ $settings->max_donor_age }} عاماً،
                                            وألا يقل وزنك عن {{ $settings->min_donor_weight }} كجم.</p>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 1.25rem;">
                                    <div
                                        style="flex-shrink: 0; width: 48px; height: 48px; background: #fdf2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #d32f2f; font-size: 1.25rem;">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div>
                                        <h4
                                            style="font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem;">
                                            العلامات الحيوية</h4>
                                        <p style="color: #718096; line-height: 1.7; font-size: 0.95rem;">سيقوم الفريق
                                            الطبي
                                            بفحص ضغط الدم ودرجة الحرارة ونبض القلب قبل التبرع.</p>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 1.25rem;">
                                    <div
                                        style="flex-shrink: 0; width: 48px; height: 48px; background: #ebf8ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #3182ce; font-size: 1.25rem;">
                                        <i class="fas fa-tint"></i>
                                    </div>
                                    <div>
                                        <h4
                                            style="font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem;">
                                            نسبة الهيموجلوبين</h4>
                                        <p style="color: #718096; line-height: 1.7; font-size: 0.95rem;">يجب أن تكون
                                            نسبة
                                            الهيموجلوبين في مستوى طبيعي (فوق 12.5 للنساء و 13.5 للرجال).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Temporary Section -->
                    <section id="temporary" style="margin-bottom: 4rem;">
                        <h2
                            style="font-size: 1.75rem; font-weight: 800; color: #1a202c; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 8px; height: 32px; background: #ed8936; border-radius: 4px;"></span>
                            موانع التبرع المؤقتة
                        </h2>
                        <div
                            style="background: #fffaf0; border: 1px solid #feebc8; border-radius: 20px; padding: 2rem;">
                            <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 1rem;">
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>تلقي
                                            اللقاحات:</strong> قد يطلب منك الانتظار من أسبوعين إلى شهر حسب نوع
                                        اللقاح.</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>استخدام
                                            المضادات الحيوية:</strong> يجب الانتظار 48-72 ساعة بعد إنهاء العلاج.</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>إجراء
                                            جراحي
                                            بسيط:</strong> الانتظار بعد {{ $settings->min_days_after_surgery }} يوماً
                                        كحد أدنى حسب الجراحة.</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>الحمل
                                            والولادة:</strong> يمنع التبرع أثناء الحمل ويسمح به بعد 6 أشهر من
                                        الولادة.</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-clock" style="color: #ed8936; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #7b341e; line-height: 1.6;"><strong>التبرع
                                            السابق:</strong> يجب الانتظار {{ $settings->min_days_between_donations }}
                                        يوماً بين كل تبرعين متتاليين.</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <!-- Permanent Section -->
                    <section id="permanent" style="margin-bottom: 4rem;">
                        <h2
                            style="font-size: 1.75rem; font-weight: 800; color: #1a202c; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                            <span style="width: 8px; height: 32px; background: #e53e3e; border-radius: 4px;"></span>
                            موانع التبرع الدائمة
                        </h2>
                        <div
                            style="background: #fff5f5; border: 1px solid #fed7d7; border-radius: 20px; padding: 2rem;">
                            <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 1rem;">
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-times-circle" style="color: #e53e3e; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #822727; line-height: 1.6;">الأمراض المعدية
                                        المزمنة (مثل التهاب الكبد الوبائي B و C).</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-times-circle" style="color: #e53e3e; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #822727; line-height: 1.6;">أمراض القلب
                                        والشرايين الحادة.</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-times-circle" style="color: #e53e3e; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #822727; line-height: 1.6;">السرطانات بجميع
                                        أنواعها (باستثناء بعض سرطانات الجلد المعافاة).</span>
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <i class="fas fa-times-circle" style="color: #e53e3e; margin-top: 5px;"></i>
                                    <span style="font-size: 0.95rem; color: #822727; line-height: 1.6;">الإصابة بمرض
                                        السكري
                                        الذي يتطلب الأنسولين (حسب البروتوكول الطبي المحلي).</span>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <!-- Pre-donation Tip Section -->
                    <section id="pre-donation">
                        <div
                            style="background: linear-gradient(135deg, #d32f2f 0%, #a12323 100%); border-radius: 20px; padding: 2.5rem; color: white; box-shadow: 0 15px 30px rgba(211, 47, 47, 0.25);">
                            <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem;">نصائح قبل يوم
                                التبرع
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-glass-whiskey"></i>
                                    <span>اشرب الكثير من السوائل</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-bed"></i>
                                    <span>احصل على قسط كافٍ من النوم</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-utensils"></i>
                                    <span>تناول وجبة فطور صحية</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="fas fa-smoking-ban"></i>
                                    <span>تجنب التدخين قبل وبعد التبرع</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <style>
        html {
            scroll-behavior: smooth;
        }

        @media (max-width: 768px) {
            .eligibility-hero h1 {
                font-size: 1.8rem !important;
            }

            .eligibility-notice-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-layout>
