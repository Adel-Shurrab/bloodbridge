<x-layout title="سياسة الخصوصية - BloodBridge">
    @push('styles')
        <style>
            .privacy-page {
                padding-top: 100px;
                background-color: #f8f9fa;
            }

            .privacy-section {
                padding: 4rem 1rem;
                background-color: #f8f9fa;
            }

            .privacy-container {
                max-width: 900px;
                margin: 0 auto;
                background: white;
                padding: 3rem;
                border-radius: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .privacy-header {
                text-align: center;
                margin-bottom: 3rem;
                border-bottom: 3px solid #d32f2f;
                padding-bottom: 2rem;
            }

            .privacy-header h1 {
                color: #2d3748;
                font-size: 2.5rem;
                font-weight: 800;
                margin-bottom: 1rem;
            }

            .last-updated {
                color: #718096;
                font-size: 0.95rem;
            }

            .table-of-contents {
                background: #f0f4ff;
                border-right: 4px solid #d32f2f;
                padding: 1.5rem;
                border-radius: 8px;
                margin-bottom: 2rem;
            }

            .table-of-contents h3 {
                color: #2d3748;
                margin-bottom: 1rem;
                font-size: 1.1rem;
            }

            .table-of-contents ul {
                list-style: none;
                padding: 0;
            }

            .table-of-contents li {
                margin: 0.5rem 0;
            }

            .table-of-contents a {
                color: #d32f2f;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .table-of-contents a:hover {
                text-decoration: underline;
                padding-right: 5px;
            }

            .content-block {
                margin-bottom: 2.5rem;
                scroll-margin-top: 120px;
            }

            .content-block h2 {
                color: #d32f2f;
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .content-block h3 {
                color: #2d3748;
                font-size: 1.1rem;
                font-weight: 600;
                margin-top: 1.5rem;
                margin-bottom: 0.75rem;
            }

            .content-block p {
                color: #4a5568;
                line-height: 1.8;
                margin-bottom: 1rem;
                font-size: 1.05rem;
            }

            .content-block ul {
                list-style-type: disc;
                margin-right: 1.5rem;
                color: #4a5568;
                line-height: 1.8;
            }

            .content-block li {
                margin-bottom: 0.75rem;
            }

            .highlight-box {
                background: #fff5f5;
                border-left: 4px solid #d32f2f;
                padding: 1rem;
                border-radius: 4px;
                margin: 1rem 0;
            }

            .highlight-box strong {
                color: #d32f2f;
            }

            .highlight-box p {
                margin: 0;
            }

            .contact-link {
                color: #d32f2f;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .contact-link:hover {
                text-decoration: underline;
            }

            @media (max-width: 768px) {
                .privacy-page {
                    padding-top: 80px;
                }

                .privacy-container {
                    padding: 1.5rem;
                }

                .privacy-header h1 {
                    font-size: 1.8rem;
                }

                .content-block h2 {
                    font-size: 1.3rem;
                }

                .table-of-contents {
                    padding: 1rem;
                }
            }
        </style>
    @endpush

    <main class="privacy-page">
        <section class="privacy-section">
            <div class="privacy-container">
                <div class="privacy-header">
                    <h1>سياسة الخصوصية</h1>
                    <p class="last-updated">آخر تحديث: {{ date('d-m-Y') }}</p>
                </div>

                <div class="table-of-contents">
                    <h3>📑 فهرس المحتويات</h3>
                    <ul>
                        <li><a href="#intro">1. مقدمة</a></li>
                        <li><a href="#data-collection">2. المعلومات التي نجمعها</a></li>
                        <li><a href="#data-usage">3. كيف نستخدم معلوماتك</a></li>
                        <li><a href="#data-sharing">4. مشاركة البيانات</a></li>
                        <li><a href="#data-security">5. أمن المعلومات</a></li>
                        <li><a href="#user-rights">6. حقوقك</a></li>
                        <li><a href="#cookies">7. ملفات تعريف الارتباط</a></li>
                        <li><a href="#retention">8. فترة الاحتفاظ بالبيانات</a></li>
                        <li><a href="#contact">9. اتصل بنا</a></li>
                    </ul>
                </div>

                <div class="privacy-content">
                    <div class="content-block" id="intro">
                        <h2>1. 🔒 مقدمة</h2>
                        <p>
                            مرحبًا بك في <strong>BloodBridge</strong> - نظام إنقاذ الأرواح عبر التبرع بالدم.
                            نحن ملتزمون بحماية خصوصيتك وأماننا بياناتك الحساسة. نظرًا للطبيعة الحساسة للمعلومات
                            الطبية التي نتعامل معها، قمنا بتصميم أنظمتنا وفقًا لأعلى معايير الأمان والشفافية الدولية.
                        </p>
                        <div class="highlight-box">
                            <p><strong>ملاحظة مهمة:</strong> هذه السياسة تتوافق مع لائحة حماية البيانات العامة (GDPR)
                                والقوانين المحلية لحماية البيانات الشخصية.</p>
                        </div>
                    </div>

                    <div class="content-block" id="data-collection">
                        <h2>2. 📊 المعلومات التي نجمعها</h2>
                        <p>لتقديم خدماتنا وإنقاذ الأرواح بكفاءة، نجمع أنواعاً معينة من البيانات:</p>

                        <h3>أ) المعلومات الشخصية:</h3>
                        <ul>
                            <li>الاسم الكامل والبريد الإلكتروني</li>
                            <li>رقم الهاتف المحمول</li>
                            <li>رقم الهوية الوطنية (للتحقق من الشخصية)</li>
                            <li>تاريخ الميلاد والجنس</li>
                            <li>العنوان الحالي والموقع الجغرافي</li>
                        </ul>

                        <h3>ب) البيانات الطبية والصحية:</h3>
                        <ul>
                            <li>فصيلة الدم ونوعها (موجب/سالب)</li>
                            <li>الوزن والطول والعمر</li>
                            <li>الأمراض المزمنة والحالات الطبية</li>
                            <li>الأدوية المستخدمة</li>
                            <li>تاريخ آخر تبرع وعدد مرات التبرع</li>
                            <li>نتائج الفحوصات الطبية (إن وجدت)</li>
                        </ul>

                        <h3>ج) بيانات الاستخدام والتكنولوجيا:</h3>
                        <ul>
                            <li>عنوان الـ IP وبيانات المتصفح</li>
                            <li>أنماط الاستخدام والعمليات التي تقوم بها</li>
                            <li>معرّفات الجهاز (Device IDs)</li>
                            <li>سجل الوقت والتاريخ للأنشطة</li>
                        </ul>
                    </div>

                    <div class="content-block" id="data-usage">
                        <h2>3. 🎯 كيف نستخدم معلوماتك</h2>
                        <p>نستخدم البيانات حصرياً للأغراض الشرعية والضرورية التالية:</p>

                        <h3>الاستخدام الأساسي:</h3>
                        <ul>
                            <li><strong>المطابقة الذكية:</strong> ربط المتبرعين بطلبات التبرع بناءً على فصيلة الدم
                                والموقع الجغرافي والحالة الصحية</li>
                            <li><strong>الإشعارات الطارئة:</strong> إرسال تنبيهات فورية عند وجود حاجة ماسة لدم في منطقتك</li>
                            <li><strong>التحقق من الأهلية:</strong> ضمان سلامة عملية التبرع من خلال التأكد من استيفاء
                                الشروط الطبية</li>
                            <li><strong>جدولة المواعيد:</strong> تنظيم مواعيد التبرع وإرسال التذكيرات</li>
                        </ul>

                        <h3>الاستخدام الثانوي:</h3>
                        <ul>
                            <li>تحسين تجربة المستخدم والخدمات</li>
                            <li>إرسال رسائل تعليمية عن فوائد التبرع بالدم</li>
                            <li>الامتثال للالتزامات القانونية والتنظيمية</li>
                            <li>الإحصائيات المجهلة لأغراض البحث الطبي</li>
                            <li>تحسين الأمان ومنع الاحتيال</li>
                        </ul>
                    </div>

                    <div class="content-block" id="data-sharing">
                        <h2>4. 🤝 مشاركة البيانات والخصوصية</h2>
                        <div class="highlight-box">
                            <p><strong>سياستنا الأساسية:</strong> نحن لا نبيع بياناتك أبداً لأي طرف ثالث.</p>
                        </div>

                        <h3>متى يتم مشاركة البيانات:</h3>
                        <ul>
                            <li><strong>عند قبول طلب تبرع:</strong> نشارك اسمك ورقم هاتفك والبيانات الطبية الضرورية
                                مع المستشفى الطالب فقط لتنسيق الموعد وضمان السلامة الطبية</li>
                            <li><strong>مع السلطات الصحية:</strong> قد نشارك البيانات عند الضرورة مع وزارة الصحة
                                للامتثال للقوانين</li>
                            <li><strong>مع مقدمي الخدمات:</strong> نشارك البيانات مع شركاء موثوقين (مثل خدمات الإرسال
                                والمدفوعات) تحت اتفاقيات سرية صارمة</li>
                        </ul>

                        <h3>حماية الخصوصية:</h3>
                        <ul>
                            <li>تظل هويتك مجهولة تماماً في الإحصائيات والتقارير العامة</li>
                            <li>لا يتم مشاركة البيانات الحساسة بدون موافقتك الصريحة</li>
                            <li>جميع الأطراف الثالثة ملتزمة بمعايير السرية والأمان</li>
                        </ul>
                    </div>

                    <div class="content-block" id="data-security">
                        <h2>5. 🔐 أمن المعلومات</h2>
                        <p>نطبق أعلى معايير الأمان لحماية بياناتك:</p>

                        <h3>تقنيات التشفير:</h3>
                        <ul>
                            <li>تشفير SSL/TLS (256-bit) لجميع البيانات أثناء النقل</li>
                            <li>تشفير AES-256 لجميع البيانات المخزنة</li>
                            <li>كلمات مرور آمنة بخوارزمية bcrypt</li>
                        </ul>

                        <h3>إجراءات الأمان:</h3>
                        <ul>
                            <li>نظام صلاحيات صارم يضمن عدم وصول أي شخص غير مصرح له إلى سجلك</li>
                            <li>عمليات تدقيق أمني منتظمة واختبارات اختراق</li>
                            <li>مراقبة مستمرة للوصول غير المصرح به</li>
                            <li>نسخ احتياطية آمنة والحماية من خسارة البيانات</li>
                        </ul>

                        <div class="highlight-box">
                            <p><strong>ملاحظة:</strong> لا نضمن أمان 100٪، لكننا نلتزم باستخدام أفضل الممارسات
                                الصناعية.</p>
                        </div>
                    </div>

                    <div class="content-block" id="user-rights">
                        <h2>6. ⚖️ حقوقك</h2>
                        <p>كمستخدم، لديك الحقوق التالية:</p>

                        <h3>حق الوصول:</h3>
                        <ul>
                            <li>الحق في الوصول إلى جميع بياناتك المسجلة لدينا في أي وقت</li>
                            <li>يمكنك طلب نسخة من بياناتك بصيغة قابلة للفهم</li>
                        </ul>

                        <h3>حق التصحيح:</h3>
                        <ul>
                            <li>تحديث ملفك الصحي أو بيانات الاتصال الخاصة بك</li>
                            <li>تصحيح أي معلومات غير صحيحة</li>
                        </ul>

                        <h3>حق الحذف (النسيان):</h3>
                        <ul>
                            <li>طلب حذف حسابك وبياناتك نهائياً من نظامنا</li>
                            <li>سيتم حذف جميع البيانات الشخصية (مع الاحتفاظ بالسجلات الطبية المطلوبة قانوناً)</li>
                        </ul>

                        <h3>حق الاعتراض:</h3>
                        <ul>
                            <li>الاعتراض على معالجة بيانات معينة</li>
                            <li>سحب موافقتك على استخدام بياناتك في أي وقت</li>
                        </ul>

                        <h3>حق الرحيل:</h3>
                        <ul>
                            <li>الحصول على بياناتك بصيغة منظمة لتحويلها إلى نظام آخر</li>
                        </ul>

                        <h3>لممارسة حقوقك:</h3>
                        <p>يرجى إرسال طلبك كتابياً عبر <a href="{{ route('contact') }}" class="contact-link">صفحة
                                الاتصال</a> مع إثبات هويتك. سنرد على طلبك خلال 30 يومًا.</p>
                    </div>

                    <div class="content-block" id="cookies">
                        <h2>7. 🍪 ملفات تعريف الارتباط (Cookies)</h2>
                        <p>نستخدم ملفات تعريف الارتباط لتحسين تجربتك:</p>

                        <h3>أنواع الملفات المستخدمة:</h3>
                        <ul>
                            <li><strong>ملفات ضرورية:</strong> للتحقق من الهوية والأمان</li>
                            <li><strong>ملفات التحليل:</strong> لفهم كيفية استخدامك للمنصة</li>
                            <li><strong>ملفات التفضيلات:</strong> لتذكر خياراتك</li>
                        </ul>

                        <p>يمكنك التحكم في ملفات تعريف الارتباط من خلال إعدادات متصفحك أو التواصل معنا.</p>
                    </div>

                    <div class="content-block" id="retention">
                        <h2>8. ⏰ فترة الاحتفاظ بالبيانات</h2>
                        <ul>
                            <li><strong>بيانات الحساب الفعال:</strong> طالما كان الحساب نشطاً</li>
                            <li><strong>السجلات الطبية:</strong> يتم الاحتفاظ بها وفقاً للقوانين الطبية (عادة 5-10 سنوات)</li>
                            <li><strong>سجلات الأنشطة:</strong> يتم حذفها بعد سنة واحدة من عدم النشاط</li>
                            <li><strong>البيانات المحذوفة:</strong> يتم محوها نهائياً بعد 90 يومًا</li>
                        </ul>
                    </div>

                    <div class="content-block" id="contact">
                        <h2>9. 📧 اتصل بنا</h2>
                        <p>إذا كان لديك أي استفسار أو شكوى حول سياسة الخصوصية أو كيفية تعاملنا مع بياناتك:</p>

                        <ul>
                            <li>🔗 <a href="{{ route('contact') }}" class="contact-link">قم بزيارة صفحة الاتصال</a></li>
                            <li>📧 البريد الإلكتروني: <strong>privacy@bloodbridge.org</strong></li>
                            <li>📞 الهاتف: <strong>+966 XX XXX XXXX</strong></li>
                        </ul>

                        <div class="highlight-box">
                            <p><strong>وقت الرد:</strong> سنرد على استفساراتك خلال 48 ساعة عمل.</p>
                        </div>
                    </div>

                    <div class="content-block">
                        <h2>📝 شروط إضافية</h2>
                        <ul>
                            <li>قد تتغير هذه السياسة من وقت لآخر. سيتم إخطارك بأي تغييرات جوهرية عبر البريد الإلكتروني</li>
                            <li>استمرار استخدامك للمنصة بعد نشر التغييرات يعني قبولك لها</li>
                            <li>لا تسري هذه السياسة على مواقع الجهات الخارجية المرتبطة بنا</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @push('scripts')
        <script>
            // Smooth scroll for table of contents
            document.querySelectorAll('.table-of-contents a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        </script>
    @endpush
</x-layout>