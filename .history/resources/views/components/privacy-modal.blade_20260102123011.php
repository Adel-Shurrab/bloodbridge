<x-modal name="privacyModal" focusable maxWidth="2xl">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-red-600 flex items-center gap-2">
                <i class="fas fa-user-shield"></i>
                سياسة الخصوصية
            </h2>
            <button @click="$dispatch('close-modal', 'privacyModal')"
                class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="space-y-6 text-gray-700 leading-relaxed overflow-y-auto max-h-[70vh] pr-2 custom-scrollbar"
            dir="rtl">
            <section>
                <h3 class="font-bold text-lg text-gray-900 mb-2">🔒 مقدمة</h3>
                <p>نحن في <strong>BloodBridge</strong> نلتزم بحماية بياناتك الصحية والشخصية وفق أعلى معايير الأمان
                    الدولية.</p>
            </section>

            <section>
                <h3 class="font-bold text-lg text-gray-900 mb-2">📊 المعلومات التي نجمعها</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>المعلومات الشخصية (الاسم، الهاتف، الهوية).</li>
                    <li>المعلومات الطبية (فصيلة الدم، التاريخ الصحي).</li>
                    <li>بيانات الموقع الجغرافي لتمكين المطابقة مع الطلبات القريبة.</li>
                </ul>
            </section>

            <section>
                <h3 class="font-bold text-lg text-gray-900 mb-2">🎯 كيف نستخدم معلوماتك</h3>
                <p>نستخدم بياناتك حصرياً لربط المتبرعين بالمحتاجين، إرسال التنبيهات الطارئة، والتحقق من الأهلية الطبية
                    للتبرع.</p>
            </section>

            <section>
                <h3 class="font-bold text-lg text-gray-900 mb-2">🤝 مشاركة البيانات</h3>
                <p>لا يتم بيع بياناتك أبداً. نشارك فقط المعلومات الضرورية (الاسم والهاتف) مع المستشفى المعني عند قبولك
                    لطلب تبرع.</p>
            </section>

            <section>
                <h3 class="font-bold text-lg text-gray-900 mb-2">🔐 أمن المعلومات</h3>
                <p>نستخدم تقنيات تشفير متطورة (SSL/AES-256) لحماية بياناتك من الوصول غير المصرح به.</p>
            </section>

            <section>
                <h3 class="font-bold text-lg text-gray-900 mb-2">⚖️ حقوقك</h3>
                <p>لديك الحق الكامل في الوصول إلى بياناتك، تصحيحها، أو طلب حذف حسابك نهائياً من نظامنا.</p>
            </section>

            <section class="bg-gray-50 p-4 rounded-lg border">
                <h3 class="font-bold text-gray-900 mb-1">📧 هل لديك استفسار؟</h3>
                <p class="text-sm">يمكنك التواصل معنا عبر البريد: <span
                        class="text-red-600">privacy@bloodbridge.org</span></p>
            </section>
        </div>

        <div class="mt-6 flex justify-end">
            <button @click="$dispatch('close-modal', 'privacyModal')"
                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-bold">
                إغلاق
            </button>
        </div>
    </div>
</x-modal>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>