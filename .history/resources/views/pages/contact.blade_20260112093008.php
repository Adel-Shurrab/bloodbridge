<x-layout title="اتصل بنا - {{ $settings->site_name }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/contact.css') }}" />
    @endpush

    <main class="contact-page">
        <section class="contact-header">
            <div class="section-header" style="text-align: center">
                <h2>{{ $settings->contact_hero_title }}</h2>
                <p>{{ $settings->contact_hero_subtitle }}</p>
            </div>
        </section>

        <section class="contact-content">
            <div class="contact-grid">
                <div class="form-card">
                    <!-- ... Form content remains same ... -->
                    <h3>أرسل لنا رسالة</h3>
                    <form id="contactForm" method="POST" action="#" class="contact-form">
                        @csrf
                        <div class="form-group">
                            <label for="name">اسمك <span class="required">*</span></label>
                            <input type="text" id="name" name="name" placeholder="ادخل اسمك" required
                                aria-required="true" aria-describedby="nameError" />
                            <span class="error-message" id="nameError"></span>
                        </div>
                        <div class="form-group">
                            <label for="email">بريدك الإلكتروني <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="name@example.com" required
                                aria-required="true" aria-describedby="emailError" />
                            <span class="error-message" id="emailError"></span>
                        </div>
                        <div class="form-group">
                            <label for="subject">الموضوع <span class="required">*</span></label>
                            <input type="text" id="subject" name="subject" placeholder="كيف يمكننا مساعدتك؟" required
                                aria-required="true" aria-describedby="subjectError" />
                            <span class="error-message" id="subjectError"></span>
                        </div>
                        <div class="form-group">
                            <label for="message">الرسالة <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="5" placeholder="اكتب رسالتك هنا..." required
                                aria-required="true" aria-describedby="messageError"></textarea>
                            <span class="error-message" id="messageError"></span>
                        </div>
                        <div class="form-group checkbox">
                            <input type="checkbox" id="privacy" name="privacy" required aria-required="true" />
                            <label for="privacy">أوافق على <a href="javascript:void(0)"
                                    @click.prevent="$dispatch('open-modal', 'privacyModal')">سياسة الخصوصية</a> <span
                                    class="required">*</span></label>
                        </div>
                        <button type="submit" class="btn btn-primary full-width" id="submitBtn">
                            <span class="btn-text">إرسال الرسالة</span>
                            <span class="btn-loader" style="display: none;">جاري الإرسال...</span>
                        </button>
                        <div class="form-message success-message" id="successMessage" style="display: none;"></div>
                        <div class="form-message error-message" id="errorMessage" style="display: none;"></div>
                    </form>
                </div>

                <div class="info-card">
                    <div class="info-section">
                        <h3>معلومات الاتصال</h3>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="info-text">
                                <h4>الهاتف</h4>
                                <a
                                    href="tel:{{ str_replace(['-', ' ', '(', ')'], '', $settings->support_phone) }}">{{ $settings->support_phone }}</a>
                                <p>{{ $settings->working_days }}، {{ $settings->working_hours }}</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div class="info-text">
                                <h4>البريد الإلكتروني</h4>
                                <a href="mailto:{{ $settings->support_email }}">{{ $settings->support_email }}</a>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="info-text">
                                <h4>العنوان</h4>
                                <p>{{ $settings->address }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-section">
                        <h3>الأسئلة الشائعة</h3>
                        @foreach($settings->contact_faqs ?? [] as $faq)
                            <div class="faq-item">
                                <button class="faq-question">
                                    <span>{{ $faq['question'] ?? '' }}</span>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <div class="faq-answer">
                                    <p>{{ $faq['answer'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    @push('scripts')
        <script src="{{ asset('assets/scripts/pages/contact.js') }}"></script>
    @endpush
</x-layout>