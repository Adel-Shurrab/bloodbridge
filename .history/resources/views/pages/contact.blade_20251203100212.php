<x-layout title="اتصل بنا - BloodBridge">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/contact.css') }}" />
    @endpush

    <main class="contact-page">
        <section class="contact-header">
            <div class="section-header">
                <h2>تواصل معنا</h2>
                <p>
                    نحن هنا لمساعدتكم والإجابة على أي سؤال لديكم. نتطلع لسماع آرائكم.
                </p>
            </div>
        </section>

        <section class="contact-content">
            <div class="contact-grid">
                <div class="form-card">
                    <h3>أرسل لنا رسالة</h3>
                    <form action="#">
                        <div class="form-group">
                            <label for="name">اسمك</label>
                            <input type="text" id="name" placeholder="ادخل اسمك" required />
                        </div>
                        <div class="form-group">
                            <label for="email">بريدك الإلكتروني</label>
                            <input type="email" id="email" placeholder="name@example.com" required />
                        </div>
                        <div class="form-group">
                            <label for="subject">الموضوع</label>
                            <input type="text" id="subject" placeholder="كيف يمكننا مساعدتك؟" required />
                        </div>
                        <div class="form-group">
                            <label for="message">الرسالة</label>
                            <textarea id="message" rows="5" placeholder="اكتب رسالتك هنا..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary full-width">
                            إرسال الرسالة
                        </button>
                    </form>
                </div>

                <div class="info-card">
                    <div class="info-section">
                        <h3>معلومات الاتصال</h3>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="info-text">
                                <h4>الهاتف</h4>
                                <a href="tel:5551234567">(555) 123-4567</a>
                                <p>الإثنين - الجمعة، 9ص - 5م</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div class="info-text">
                                <h4>البريد الإلكتروني</h4>
                                <a href="mailto:support@bloodbridge.org">support@bloodbridge.org</a>
                            </div>
                        </div>
                    </div>

                    <div class="faq-section">
                        <h3>الأسئلة الشائعة</h3>
                        <div class="faq-item">
                            <button class="faq-question">
                                <span>كيف يمكنني جدولة التبرع بالدم؟</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="faq-answer">
                                <p>
                                    يمكنك بسهولة جدولة موعد من خلال لوحة التحكم الخاصة بك بعد
                                    تسجيل الدخول. اختر المركز الأقرب إليك والوقت المناسب.
                                </p>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">
                                <span>ما هي متطلبات الأهلية للتبرع؟</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="faq-answer">
                                <p>
                                    يجب أن يكون عمرك 18 عامًا على الأقل، وتتمتع بصحة جيدة، وتزن
                                    50 كجم على الأقل. سيتم إجراء فحص صحي سريع قبل التبرع.
                                </p>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">
                                <span>كم من الوقت تستغرق عملية التبرع؟</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <div class="faq-answer">
                                <p>
                                    التبرع الفعلي بالدم يستغرق حوالي 10-15 دقيقة. العملية
                                    بأكملها، بما في ذلك التسجيل والفحص والراحة، تستغرق حوالي
                                    ساعة واحدة.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @push('scripts')
        <script src="{{ asset('assets/scripts/pages/contact.js') }}"></script>
    @endpush
</x-layout>