@props(['name' => 'privacyModal'])

<x-modal :name="$name" maxWidth="2xl">
    <div class="p-6 bg-white rounded-lg">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">سياسة الخصوصية</h2>
            <button 
                @click="show = false" 
                class="text-gray-500 hover:text-gray-700 text-2xl leading-none"
                aria-label="إغلاق">
                ×
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-4">آخر تحديث: {{ date('d-m-Y') }}</p>

        <!-- Content -->
        <div class="space-y-4 max-h-[70vh] overflow-y-auto">
            <!-- Intro -->
            <div>
                <h3 class="text-lg font-semibold text-red-600 mb-2">🔒 مقدمة</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    مرحبًا بك في <strong>BloodBridge</strong> - نظام إنقاذ الأرواح عبر التبرع بالدم.
                    نحن ملتزمون بحماية خصوصيتك وأمان بياناتك الحساسة بأعلى معايير الأمان والشفافية الدولية.
                </p>
            </div>

            <!-- Data Collection -->
            <div>
                <h3 class="text-lg font-semibold text-red-600 mb-2">📊 البيانات التي نجمعها</h3>
                <ul class="text-sm text-gray-700 space-y-1 mr-4">
                    <li>✓ بيانات شخصية: الاسم، البريد الإلكتروني، رقم الهاتف، الهوية الوطنية</li>
                    <li>✓ بيانات صحية: فصيلة الدم، الوزن، الأمراض المزمنة، سجل التبرعات</li>
                    <li>✓ بيانات الموقع: العنوان والموقع الجغرافي</li>
                    <li>✓ بيانات التكنولوجيا: عنوان IP، أنماط الاستخدام</li>
                </ul>
            </div>

            <!-- Data Usage -->
            <div>
                <h3 class="text-lg font-semibold text-red-600 mb-2">🎯 استخدام بياناتك</h3>
                <ul class="text-sm text-gray-700 space-y-1 mr-4">
                    <li>• ربط المتبرعين بطلبات التبرع المطابقة</li>
                    <li>• إرسال إشعارات حالات الطوارئ</li>
                    <li>• التحقق من أهليتك الطبية</li>
                    <li>• جدولة وتذكير المواعيد</li>
                    <li>• تحسين الخدمة والأمان</li>
                </ul>
            </div>

            <!-- Data Sharing -->
            <div class="bg-red-50 border-r-4 border-red-600 p-3 rounded">
                <h3 class="text-lg font-semibold text-red-600 mb-2">🤝 مشاركة البيانات</h3>
                <p class="text-sm text-gray-700">
                    <strong>سياستنا الأساسية: لا نبيع بياناتك أبداً.</strong> نشارك بيانات مع المستشفيات فقط عند قبول طلب تبرع، 
                    ومع السلطات الصحية للامتثال للقوانين. جميع الأطراف الثالثة ملتزمة بالسرية.
                </p>
            </div>

            <!-- Security -->
            <div>
                <h3 class="text-lg font-semibold text-red-600 mb-2">🔐 أمن المعلومات</h3>
                <ul class="text-sm text-gray-700 space-y-1 mr-4">
                    <li>✓ تشفير SSL/TLS (256-bit) لجميع البيانات أثناء النقل</li>
                    <li>✓ تشفير AES-256 للبيانات المخزنة</li>
                    <li>✓ كلمات مرور آمنة بخوارزمية bcrypt</li>
                    <li>✓ نظام صلاحيات صارم وعمليات تدقيق أمني منتظمة</li>
                </ul>
            </div>

            <!-- User Rights -->
            <div>
                <h3 class="text-lg font-semibold text-red-600 mb-2">⚖️ حقوقك</h3>
                <ul class="text-sm text-gray-700 space-y-1 mr-4">
                    <li>✓ حق الوصول إلى بياناتك في أي وقت</li>
                    <li>✓ حق تصحيح أو تحديث معلوماتك</li>
                    <li>✓ حق طلب حذف حسابك ودعاتك</li>
                    <li>✓ حق الاعتراض على معالجة معينة</li>
                    <li>✓ حق الرحيل - الحصول على بياناتك لتحويلها</li>
                </ul>
            </div>

            <!-- Retention -->
            <div>
                <h3 class="text-lg font-semibold text-red-600 mb-2">⏰ فترة الاحتفاظ بالبيانات</h3>
                <ul class="text-sm text-gray-700 space-y-1 mr-4">
                    <li>• بيانات الحساب: طالما الحساب نشطاً</li>
                    <li>• السجلات الطبية: 5-10 سنوات حسب القانون</li>
                    <li>• سجلات الأنشطة: حذف بعد سنة من عدم النشاط</li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="bg-blue-50 border-r-4 border-blue-600 p-3 rounded">
                <h3 class="text-lg font-semibold text-blue-600 mb-2">📧 اتصل بنا</h3>
                <p class="text-sm text-gray-700">
                    للاستفسارات أو الشكاوى حول الخصوصية:<br>
                    <strong>privacy@bloodbridge.org</strong><br>
                    سنرد خلال 48 ساعة عمل.
                </p>
            </div>

            <!-- Footer Note -->
            <div class="pt-2 border-t">
                <p class="text-xs text-gray-500">
                    هذه السياسة تتوافق مع لائحة حماية البيانات العامة (GDPR) والقوانين المحلية.
                </p>
            </div>
        </div>

        <!-- Action Button -->
        <div class="mt-6 flex justify-end">
            <button 
                @click="show = false"
                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                فهمت واغلق
            </button>
        </div>
    </div>
</x-modal>
