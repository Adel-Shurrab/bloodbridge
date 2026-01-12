@props(['name' => 'privacyModal'])

<x-modal :name="$name" maxWidth="2xl">
    <div class="privacy-modal-container"
        style="background: white !important; color: #1a202c !important; font-family: 'Cairo', sans-serif;">
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
                border-bottom: 2px solid #f3f4f6;
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

            .privacy-modal-close {
                background: none;
                border: none;
                color: #9ca3af;
                cursor: pointer;
                font-size: 1.25rem;
                transition: color 0.2s;
                padding: 0.5rem;
            }

            .privacy-modal-close:hover {
                color: #4b5563;
            }

            .privacy-modal-content {
                max-height: 60vh;
                overflow-y: auto;
                padding-left: 1rem;
            }

            /* Custom Scrollbar */
            .privacy-modal-content::-webkit-scrollbar {
                width: 6px;
            }

            .privacy-modal-content::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .privacy-modal-content::-webkit-scrollbar-thumb {
                background: #d32f2f;
                border-radius: 10px;
            }

            .privacy-modal-content::-webkit-scrollbar-thumb:hover {
                background: #b71c1c;
            }

            .privacy-section {
                margin-bottom: 2rem;
            }

            .privacy-section h3 {
                font-size: 1.1rem;
                font-weight: 700;
                color: #b71c1c;
                margin-bottom: 0.75rem;
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
            }

            .privacy-list li {
                position: relative;
                padding-right: 1.5rem;
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
                color: #4a5568;
            }

            .privacy-list li::before {
                content: '•';
                color: #d32f2f;
                position: absolute;
                right: 0;
                font-weight: bold;
            }

            .privacy-highlight {
                background: #fff5f5;
                border-right: 4px solid #d32f2f;
                padding: 1rem;
                border-radius: 0 4px 4px 0;
                font-weight: 600;
                color: #2d3748;
            }

            .privacy-modal-footer {
                margin-top: 1.5rem;
                padding-top: 1rem;
                border-top: 2px solid #f3f4f6;
                display: flex;
                justify-content: flex-end;
            }

            .close-btn {
                background: #d32f2f;
                color: white;
                border: none;
                padding: 0.6rem 2rem;
                border-radius: 8px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.2s;
                font-family: 'Cairo', sans-serif;
            }

            .close-btn:hover {
                background: #b71c1c;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(211, 47, 47, 0.2);
            }
        </style>

        <div class="privacy-modal-header">
            <h2 class="privacy-modal-title">
                <i class="fas fa-shield-alt"></i>
                سياسة الخصوصية
            </h2>
            <button @click="$dispatch('close-modal', '{{ $name }}')" class="privacy-modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="privacy-modal-content">
            <div class="privacy-section">
                <h3>1. مقدمة</h3>
                <p>
                    مرحباً بك في {{ app(\App\Settings\GeneralSettings::class)->site_name }}. نحن نولي أهمية قصوى
                    لخصوصيتك وأمان بياناتك الصحية، ونعمل وفق أعلى معايير
                    الحماية لضمان سرية معلوماتك.
                </p>
            </div>

            <div class="privacy-section">
                <h3>2. المعلومات التي نجمعها</h3>
                <ul class="privacy-list">
                    <li>المعلومات الشخصية: الاسم، رقم الجوال، رقم الهوية.</li>
                    <li>المعلومات الصحية: فصيلة الدم، الحالات الطبية، وتاريخ التبرع.</li>
                    <li>بيانات تقنية: عنوان IP ونوع المتصفح لتحسين الأمان.</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h3>3. كيف نستخدم معلوماتك</h3>
                <p>
                    نستخدم بياناتك حصرياً لتنسيق عمليات التبرع، إرسال التنبيهات الضرورية، والتحقق من الأهلية الطبية
                    لضمان سلامتك وسلامة المستقبلين.
                </p>
            </div>

            <div class="privacy-section">
                <div class="privacy-highlight">
                    نحن لا نبيع بياناتك أبداً. يتم مشاركة معلوماتك فقط مع المستشفى المعني عند قبولك لطلب التبرع لضمان
                    التنسيق الطبي الصحيح.
                </div>
            </div>

            <div class="privacy-section">
                <h3>4. أمن المعلومات</h3>
                <ul class="privacy-list">
                    <li>تشفير SSL/TLS لجميع البيانات المتنقلة.</li>
                    <li>تشفير AES-256 للبيانات المخزنة.</li>
                    <li>رقابة صارمة على صلاحيات الوصول للسجلات الطبية.</li>
                </ul>
            </div>

            <div class="privacy-section" style="margin-bottom: 0;">
                <h3>5. حقوقك</h3>
                <p>
                    لديك الحق التام في الوصول إلى بياناتك، تصحيحها، أو طلب حذف حسابك نهائياً من نظامنا عبر صفحة الاتصال.
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