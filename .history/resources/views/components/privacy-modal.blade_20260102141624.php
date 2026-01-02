@props(['name' => 'privacyModal'])

<x-modal :name="$name" maxWidth="2xl">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-shield-alt text-red-600"></i>
                سياسة الخصوصية
            </h2>
            <button @click="$dispatch('close-modal', '{{ $name }}')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="max-h-[60vh] overflow-y-auto pr-2 text-right" dir="rtl">
            <div class="space-y-6">
                <section>
                    <h3 class="font-bold text-red-600 mb-2">1. مقدمة</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        مورحبًا بك في BloodBridge. نحن نولي أهمية قصوى لخصوصيتك وأمان بياناتك الصحية، ونعمل وفق أعلى
                        معايير الحماية لضمان سرية معلوماتك.
                    </p>
                </section>

                <section>
                    <h3 class="font-bold text-red-600 mb-2">2. المعلومات التي نجمعها</h3>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        <li>المعلومات الشخصية: الاسم، رقم الجوال، رقم الهوية.</li>
                        <li>المعلومات الصحية: فصيلة الدم، الحالات الطبية، وتاريخ التبرع.</li>
                        <li>بيانات تقنية: عنوان IP ونوع المتصفح لتحسين الأمان.</li>
                    </ul>
                </section>

                <section>
                    <h3 class="font-bold text-red-600 mb-2">3. كيف نستخدم معلوماتك</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        نستخدم بياناتك حصرياً لتنسيق عمليات التبرع، إرسال التنبيهات الضرورية، والتحقق من الأهلية الطبية
                        لضمان سلامتك وسلامة المستقبلين.
                    </p>
                </section>

                <section>
                    <h3 class="font-bold text-red-600 mb-2">4. مشاركة البيانات</h3>
                    <p class="text-gray-600 leading-relaxed text-sm font-bold border-r-4 border-red-500 pr-3">
                        نحن لا نبيع بياناتك أبداً. يتم مشاركة معلوماتك فقط مع المستشفى المعني عند قبولك لطلب التبرع
                        لضمان التنسيق الطبي الصحيح.
                    </p>
                </section>

                <section>
                    <h3 class="font-bold text-red-600 mb-2">5. أمن المعلومات</h3>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        <li>تشفير SSL/TLS لجميع البيانات المتنقلة.</li>
                        <li>تشفير AES-256 للبيانات المخزنة.</li>
                        <li>رقابة صارمة على صلاحيات الوصول للسجلات الطبية.</li>
                    </ul>
                </section>

                <section>
                    <h3 class="font-bold text-red-600 mb-2">6. حقوقك</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">
                        لديك الحق التام في الوصول إلى بياناتك، تصحيحها، أو طلب حذف حسابك نهائياً من نظامنا عبر صفحة
                        الاتصال.
                    </p>
                </section>
            </div>
        </div>

        <div class="mt-6 flex justify-end pt-3 border-t">
            <button @click="$dispatch('close-modal', '{{ $name }}')"
                class="btn btn-primary px-6 py-2 rounded-lg text-sm">
                إغلاق
            </button>
        </div>
    </div>
</x-modal>