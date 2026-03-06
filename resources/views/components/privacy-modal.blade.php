@props(['name' => 'privacyModal'])

<x-modal :name="$name" maxWidth="2xl">
    <div class="privacy-modal-container"
        style="background: #ffffff; color: #2d3748; font-family: 'Cairo', sans-serif; position: relative; overflow: hidden; width: 100%;">

        <!-- Decorative Background Element -->
        <div
            style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; background: rgba(211, 47, 47, 0.03); border-radius: 50%; z-index: 0;">
        </div>

        <style>
            .privacy-modal-container {
                padding: 1.5rem;
                direction: rtl;
                text-align: right;
            }

            .privacy-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #f7fafc;
                position: relative;
                z-index: 1;
            }

            .privacy-modal-title-wrapper {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }

            .privacy-modal-title {
                font-size: 1.5rem;
                font-weight: 800;
                color: #d32f2f;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin: 0;
            }

            .privacy-last-updated {
                font-size: 0.7rem;
                color: #a0aec0;
                font-weight: 600;
                margin-right: 2.5rem;
            }

            .privacy-modal-close {
                background: #f7fafc;
                border: none;
                color: #718096;
                cursor: pointer;
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .privacy-modal-close:hover {
                background: #edf2f7;
                color: #2d3748;
                transform: rotate(90deg);
            }

            .privacy-modal-content {
                max-height: 60vh;
                overflow-y: auto;
                padding-left: 1rem;
                padding-right: 0.25rem;
                position: relative;
                z-index: 1;
            }

            /* Custom Premium Scrollbar */
            .privacy-modal-content::-webkit-scrollbar {
                width: 6px;
            }

            .privacy-modal-content::-webkit-scrollbar-track {
                background: #f8fafc;
                border-radius: 10px;
            }

            .privacy-modal-content::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 10px;
            }

            .privacy-modal-content::-webkit-scrollbar-thumb:hover {
                background: #d32f2f;
            }

            .privacy-section {
                margin-bottom: 2rem;
                position: relative;
            }

            .privacy-section-header {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                margin-bottom: 0.75rem;
            }

            .privacy-section-icon {
                width: 28px;
                height: 28px;
                background: #fff5f5;
                color: #d32f2f;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.8rem;
            }

            .privacy-section h3 {
                font-size: 1.1rem;
                font-weight: 700;
                color: #1a202c;
                margin: 0;
            }

            .privacy-section p {
                font-size: 0.95rem;
                line-height: 1.7;
                color: #4a5568;
                margin: 0;
            }

            .privacy-list {
                list-style: none;
                padding: 0;
                margin: 0.75rem 0 0 0;
                display: grid;
                gap: 0.5rem;
            }

            .privacy-list li {
                position: relative;
                padding-right: 1.5rem;
                font-size: 0.9rem;
                color: #4a5568;
                line-height: 1.5;
            }

            .privacy-list li::before {
                content: '\f00c';
                font-family: 'Font Awesome 6 Free';
                font-weight: 900;
                color: #38a169;
                position: absolute;
                right: 0;
                top: 2px;
                font-size: 0.75rem;
            }

            .privacy-highlight {
                background: linear-gradient(to left, #fff5f5, #ffffff);
                border-right: 3px solid #d32f2f;
                padding: 1rem;
                border-radius: 0 6px 6px 0;
                font-weight: 600;
                color: #2d3748;
                font-size: 0.9rem;
                line-height: 1.6;
            }

            .privacy-modal-footer {
                margin-top: 1.5rem;
                padding-top: 1rem;
                border-top: 2px solid #f7fafc;
                display: flex;
                justify-content: flex-end;
                z-index: 1;
                position: relative;
            }

            .close-btn {
                background: #d32f2f;
                color: white;
                border: none;
                padding: 0.6rem 2rem;
                border-radius: 10px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Cairo', sans-serif;
                font-size: 0.95rem;
            }

            .close-btn:hover {
                background: #b71c1c;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(211, 47, 47, 0.2);
            }

            @media (max-width: 640px) {
                .privacy-modal-container {
                    padding: 1.25rem;
                }

                .privacy-modal-title {
                    font-size: 1.25rem;
                }

                .close-btn {
                    width: 100%;
                }
            }
        </style>

        <div class="privacy-modal-header">
            <div class="privacy-modal-title-wrapper">
                <h2 class="privacy-modal-title">
                    <i class="fas fa-shield-heart"></i>
                    سياسة الخصوصية
                </h2>
                <span class="privacy-last-updated">آخر تحديث: مارس 2026</span>
            </div>
            <button @click="$dispatch('close-modal', '{{ $name }}')" class="privacy-modal-close"
                aria-label="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="privacy-modal-content">
            <div class="privacy-section">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-info-circle"></i></div>
                    <h3>1. مقدمة</h3>
                </div>
                <p>
                    مرحباً بك في {{ app(\App\Settings\GeneralSettings::class)->site_name }}. نحن نولي أهمية قصوى
                    لخصوصيتك وأمان بياناتك الصحية، ونعمل وفق أعلى معايير
                    الحماية لضمان سرية معلوماتك وثقتك بنا كجسر لإنقاذ الأرواح.
                </p>
            </div>

            <div class="privacy-section">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-id-card"></i></div>
                    <h3>2. المعلومات التي نجمعها</h3>
                </div>
                <ul class="privacy-list">
                    <li><strong>المعلومات الشخصية:</strong> الاسم، رقم الجوال، ورقم الهوية.</li>
                    <li><strong>المعلومات الصحية:</strong> فصيلة الدم، الحالات الطبية، وتاريخ التبرع.</li>
                    <li><strong>الموقع:</strong> المدينة والمنطقة لتسهيل التبرع.</li>
                    <li><strong>بيانات تقنية:</strong> عنوان IP ونوع المتصفح للأمان.</li>
                </ul>
            </div>

            <div class="privacy-section">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <h3>3. كيف نستخدم معلوماتك</h3>
                </div>
                <ul class="privacy-list">
                    <li>تنسيق عمليات التبرع بالدم.</li>
                    <li>إرسال تنبيهات الحالات الطارئة.</li>
                    <li>التحقق التلقائي من الأهلية الطبية.</li>
                </ul>
            </div>

            <div class="privacy-section">
                <div class="privacy-highlight">
                    <i class="fas fa-user-lock" style="margin-left: 0.5rem; color: #d32f2f;"></i>
                    <strong>أمانك أولاً:</strong> نحن لا نبيع بياناتك. يتم مشاركة معلوماتك فقط مع المستشفى المعني عند
                    قبولك لطلب التبرع.
                </div>
            </div>

            <div class="privacy-section">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-lock"></i></div>
                    <h3>4. أمن المعلومات وحمايتها</h3>
                </div>
                <ul class="privacy-list">
                    <li>استخدام بروتوكولات تشفير متقدمة (SSL/TLS) لحماية البيانات أثناء النقل.</li>
                    <li>تخزين البيانات الحساسة باستخدام خوارزميات تشفير AES-256 المتطورة.</li>
                    <li>أنظمة مراقبة على مدار الساعة للكشف عن أي محاولات وصول غير مصرح بها.</li>
                </ul>
            </div>

            <div class="privacy-section" style="margin-bottom: 0;">
                <div class="privacy-section-header">
                    <div class="privacy-section-icon"><i class="fas fa-user-check"></i></div>
                    <h3>5. حقوقك</h3>
                </div>
                <p>
                    لديك الحق التام في الوصول إلى بياناتك، تصحيحها، أو طلب حذف حسابك نهائياً عبر صفحة الاتصال.
                </p>
            </div>
        </div>

        <div class="privacy-modal-footer">
            <button @click="$dispatch('close-modal', '{{ $name }}')" class="close-btn">
                إغلاق
            </button>
        </div>
    </div>
</x-modal>
