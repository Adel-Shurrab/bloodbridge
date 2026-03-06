<x-layout title="تأكيد البريد الإلكتروني - {{ $settings->site_name }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/styles/pages/verify-email.css') }}" />
    @endpush

    <section class="ve-section">

        {{-- Animated background blobs --}}
        <div class="ve-background">
            <div class="ve-blob ve-blob-1"></div>
            <div class="ve-blob ve-blob-2"></div>
        </div>

        <div class="ve-container">

            {{-- ── Form Card ── --}}
            <div class="ve-card">

                {{-- Header --}}
                <div class="ve-header">
                    <div class="ve-icon-wrap">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <h1>تأكيد البريد الإلكتروني</h1>
                    <p>
                        شكراً لانضمامك إلينا! قبل البدء، يرجى تأكيد عنوان بريدك الإلكتروني من خلال النقر على الرابط الذي
                        أرسلناه إليك للتو. إذا لم تتلقَ البريد الإلكتروني، سنرسل لك واحداً آخر بكل سرور.
                    </p>
                </div>

                {{-- Success status alert --}}
                @if (session('status') == 'verification-link-sent')
                    <div class="ve-status-alert" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>تم إرسال رابط تأكيد جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.</span>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="ve-actions">
                    <form method="POST" action="{{ route('verification.send') }}" id="resendVerificationForm">
                        @csrf
                        <button type="submit" class="ve-submit-btn" id="submitBtn">
                            <i class="fa-solid fa-paper-plane btn-text"></i>
                            <span class="btn-text">إعادة إرسال رابط التأكيد</span>
                            <span class="btn-loader"></span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="ve-logout-form">
                        @csrf
                        <button type="submit" class="ve-logout-btn">
                            تسجيل الدخول بحساب آخر؟
                        </button>
                    </form>
                </div>

            </div>

            {{-- ── Illustration Panel ── --}}
            <div class="ve-illustration">
                <div class="ve-illustration-content">

                    <span class="ve-illustration-icon">🛡️</span>

                    <h2>تفعيل الحساب والأمان</h2>
                    <p>لحماية بياناتك وضمان التواصل الفعال، نطلب تأكيد ملكية البريد الإلكتروني الخاص بك.</p>

                    <div class="ve-steps">
                        <div class="ve-step">
                            <span class="ve-step-num">١</span>
                            <span class="ve-step-text">افتح صندوق الوارد في بريدك الإلكتروني</span>
                        </div>
                        <div class="ve-step">
                            <span class="ve-step-num">٢</span>
                            <span class="ve-step-text">ابحث عن رسالة التفعيل من منصتنا</span>
                        </div>
                        <div class="ve-step">
                            <span class="ve-step-num">٣</span>
                            <span class="ve-step-text">انقر على زر التأكيد للبدء</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    @push('scripts')
        <script>
            // Loading state on submit
            const form = document.getElementById('resendVerificationForm');
            const btn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', () => {
                    btn.classList.add('loading');
                    btn.disabled = true;
                });
            }
        </script>
    @endpush
</x-layout>
