<x-public-layout>
    <x-slot name="title">سياسة الخصوصية - BloodBridge</x-slot>

    @push('styles')
        <style>
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
            }

            .privacy-header h1 {
                color: #2d3748;
                font-size: 2.5rem;
                font-weight: 800;
                margin-bottom: 1rem;
            }

            .last-updated {
                color: #718096;
                font-size: 0.9rem;
            }

            .content-block {
                margin-bottom: 2.5rem;
            }

            .content-block h2 {
                color: #e53e3e;
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
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
                margin-bottom: 0.5rem;
            }
        </style>
    @endpush

    <section class="privacy-section">
        <div class="privacy-container">
            <div class="privacy-header">
                <h1>سياسة الخصوصية</h1>
                <p class="last-updated">آخر تحديث: {{ date('d-m-Y') }}</p>
            </div>

            <div class="privacy-content">
                <div class="content-block">
                    <h2>1. مقدمة</h2>
                    <p>
                        نحن في <strong>BloodBridge</strong> نأخذ خصوصيتك على محمل الجد. نظرًا للطبيعة الحساسة للبيانات
                        الصحية التي نتعامل معها، قمنا بتصميم أنظمتنا وفقًا لأعلى معايير الأمان والشفافية. توضح هذه
                        السياسة كيف نجمع بياناتك ونستخدمها ونحميها.
                    </p>
                </div>

                <div class="content-block">
                    <h2>2. المعلومات التي نجمعها</h2>
                    <p>لتقديم خدماتنا وإنقاذ الأرواح، نحتاج إلى جمع أنواع معينة من البيانات:</p>
                    <ul>
                        <li><strong>المعلومات الشخصية:</strong> الاسم الكامل، رقم الهوية الوطنية (للتحقق من الشخصية)،
                            رقم الهاتف، والبريد الإلكتروني.</li>
                        <li><strong>الملف الصحي:</strong> فصيلة الدم، الوزن، الطول، الأمراض المزمنة، وتاريخ آخر تبرع.
                            [cite_start]هذه البيانات ضرورية لتحديد أهليتك للتبرع[cite: 114].</li>
                        [cite_start]<li><strong>بيانات الموقع الجغرافي:</strong> نستخدم موقعك الحالي لربطك بطلبات التبرع
                            العاجلة في محيطك الجغرافي[cite: 97].</li>
                    </ul>
                </div>

                <div class="content-block">
                    <h2>3. كيف نستخدم معلوماتك</h2>
                    <p>نستخدم البيانات حصرياً للأغراض التالية:</p>
                    <ul>
                        [cite_start]<li><strong>المطابقة الذكية:</strong> ربط المتبرعين بالمستشفيات بناءً على فصيلة الدم
                            والموقع الجغرافي[cite: 102].</li>
                        [cite_start]<li><strong>الإشعارات الطارئة:</strong> إرسال تنبيهات فورية عند وجود حاجة ماسة لدم
                            في منطقتك[cite: 99].</li>
                        [cite_start]<li><strong>التحقق من الأهلية:</strong> ضمان سلامة عملية التبرع من خلال التأكد من
                            استيفاء الشروط الطبية (مثل قاعدة الـ 90 يومًا)[cite: 122].</li>
                    </ul>
                </div>

                <div class="content-block">
                    <h2>4. مشاركة البيانات والخصوصية</h2>
                    <p>
                        <strong>نحن لا نبيع بياناتك أبداً.</strong> تتم مشاركة معلوماتك فقط في الحالات التالية:
                    </p>
                    <ul>
                        [cite_start]<li>عندما تقوم <strong>بقبول</strong> طلب تبرع، نشارك اسمك ورقم هاتفك مع المستشفى
                            الطالب فقط لتنسيق الموعد[cite: 311].</li>
                        <li>تظل هويتك مجهولة تماماً أثناء عمليات البحث العامة التي تجريها المستشفيات.</li>
                    </ul>
                </div>

                <div class="content-block">
                    <h2>5. أمن المعلومات</h2>
                    <p>
                        نستخدم تقنيات تشفير متقدمة (TLS/AES-256) لحماية بياناتك أثناء النقل والتخزين. [cite_start]كما
                        نطبق نظام صلاحيات صارم يضمن عدم وصول أي شخص غير مصرح له إلى سجلك الطبي[cite: 230].
                    </p>
                </div>

                <div class="content-block">
                    <h2>6. حقوقك</h2>
                    <p>كمستخدم، لديك الحق في:</p>
                    <ul>
                        <li>الوصول إلى جميع بياناتك المسجلة لدينا في أي وقت.</li>
                        <li>تحديث ملفك الصحي أو بيانات الاتصال الخاصة بك.</li>
                        <li>طلب حذف حسابك وبياناتك نهائياً من نظامنا.</li>
                    </ul>
                </div>

                <div class="content-block">
                    <h2>7. اتصل بنا</h2>
                    <p>
                        إذا كان لديك أي استفسار حول سياسة الخصوصية، يرجى التواصل معنا عبر صفحة <a
                            href="{{ route('contact') }}" style="color: #e53e3e; text-decoration: underline;">اتصل
                            بنا</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>