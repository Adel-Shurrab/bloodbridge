<x-filament-panels::page>
    @php
        $data = $this->getIneligibilityData();
        $reasonLabel = match ($data['reason']) {
            'blood_virus' => 'وجود فيروسات في الدم (HCV/HBV/HIV)',
            'chronic_disease' => 'مرض مزمن يمنع التبرع',
            'heart_disease' => 'أمراض القلب',
            'cancer' => 'تاريخ مرضي للسرطان',
            'other_perm' => 'أسباب طبية دائمة أخرى',
            default => $data['reason'],
        };

        $reasonIcon = match ($data['reason']) {
            'blood_virus' => 'heroicon-o-beaker',
            'chronic_disease' => 'heroicon-o-heart',
            'heart_disease' => 'heroicon-o-heart',
            'cancer' => 'heroicon-o-shield-exclamation',
            default => 'heroicon-o-document-text',
        };
    @endphp

    {{-- ─────────── Page Shell ─────────── --}}
    <div class="mi-shell">

        {{-- Ambient background blobs --}}
        <div class="mi-blob mi-blob--1" aria-hidden="true"></div>
        <div class="mi-blob mi-blob--2" aria-hidden="true"></div>

        <div class="mi-center">

            {{-- ── Wordmark / Brand bar ── --}}
            <div class="mi-brand" aria-label="BloodBridge">
                <span class="mi-brand__drop" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 2C12 2 4 9.5 4 14.5C4 18.6421 7.58172 22 12 22C16.4183 22 20 18.6421 20 14.5C20 9.5 12 2 12 2Z" />
                    </svg>
                </span>
                <span class="mi-brand__name">BloodBridge</span>
            </div>

            {{-- ── Main Card ── --}}
            <div class="mi-card" role="main">

                {{-- Danger stripe at top --}}
                <div class="mi-card__stripe" aria-hidden="true"></div>

                <div class="mi-card__body">

                    {{-- ── Icon Badge ── --}}
                    <div class="mi-icon-badge" aria-hidden="true">
                        <div class="mi-icon-badge__ring mi-icon-badge__ring--outer"></div>
                        <div class="mi-icon-badge__ring mi-icon-badge__ring--inner"></div>
                        <div class="mi-icon-badge__core">
                            <x-filament::icon icon="heroicon-o-no-symbol" class="mi-icon-badge__svg" />
                        </div>
                    </div>

                    {{-- ── Title block ── --}}
                    <div class="mi-title-block">
                        <p class="mi-eyebrow">قرار طبي نهائي</p>
                        <h1 class="mi-title">حساب مقيّد طبياً</h1>
                        <div class="mi-status-pill" role="status" aria-label="استبعاد طبي دائم">
                            <span class="mi-status-pill__dot" aria-hidden="true"></span>
                            استبعاد طبي دائم
                        </div>
                    </div>

                    {{-- ── Description ── --}}
                    <p class="mi-desc">
                        بناءً على التقييم الطبي الأخير، تم تسجيلك كمتبرع غير لائق طبياً بشكل دائم.
                        هذا الإجراء ضروري لضمان سلامتك وسلامة مستقبلي الدم.
                    </p>

                    {{-- ── Detail Panel ── --}}
                    <div class="mi-detail-panel" role="region" aria-label="تفاصيل الاستبعاد">
                        <div class="mi-detail-panel__header">
                            <x-filament::icon icon="heroicon-m-clipboard-document-list"
                                class="mi-detail-panel__header-icon" />
                            <span>تفاصيل الاستبعاد الطبي</span>
                        </div>

                        <div class="mi-detail-panel__rows">
                            {{-- Row: Organization --}}
                            <div class="mi-detail-row">
                                <div class="mi-detail-row__icon-wrap" aria-hidden="true">
                                    <x-filament::icon icon="heroicon-m-building-office-2" class="mi-detail-row__icon" />
                                </div>
                                <div class="mi-detail-row__content">
                                    <span class="mi-detail-row__label">جهة التقييم</span>
                                    <span class="mi-detail-row__value">{{ $data['organization_name'] }}</span>
                                </div>
                            </div>

                            {{-- Row: Date --}}
                            <div class="mi-detail-row">
                                <div class="mi-detail-row__icon-wrap" aria-hidden="true">
                                    <x-filament::icon icon="heroicon-m-calendar-days" class="mi-detail-row__icon" />
                                </div>
                                <div class="mi-detail-row__content">
                                    <span class="mi-detail-row__label">تاريخ القرار</span>
                                    <span class="mi-detail-row__value">{{ $data['date'] }}</span>
                                </div>
                            </div>

                            {{-- Row: Reason (highlighted) --}}
                            <div class="mi-detail-row mi-detail-row--reason">
                                <div class="mi-detail-row__icon-wrap mi-detail-row__icon-wrap--danger"
                                    aria-hidden="true">
                                    <x-filament::icon icon="{{ $reasonIcon }}"
                                        class="mi-detail-row__icon mi-detail-row__icon--danger" />
                                </div>
                                <div class="mi-detail-row__content">
                                    <span class="mi-detail-row__label">السبب الطبي الموثّق</span>
                                    <span
                                        class="mi-detail-row__value mi-detail-row__value--danger">{{ $reasonLabel }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Notice bar ── --}}
                    <div class="mi-notice" role="note">
                        <x-filament::icon icon="heroicon-m-information-circle" class="mi-notice__icon" />
                        <p class="mi-notice__text">
                            إذا كنت تعتقد أن هذا القرار صدر بالخطأ، يُرجى التواصل مع الدعم الفني أو مراجعة الجهة الصحية
                            المختصة.
                        </p>
                    </div>

                    {{-- ── Actions ── --}}
                    <div class="mi-actions">
                        <a href="mailto:support@bloodbridge.ps" class="mi-btn mi-btn--secondary">
                            <x-filament::icon icon="heroicon-m-envelope" class="mi-btn__icon" />
                            <span>تواصل مع الدعم</span>
                        </a>

                        <form action="{{ route('filament.donor.auth.logout') }}" method="post" class="mi-logout-form">
                            @csrf
                            <button type="submit" class="mi-btn mi-btn--ghost">
                                <x-filament::icon icon="heroicon-m-arrow-right-on-rectangle" class="mi-btn__icon" />
                                <span>تسجيل الخروج</span>
                            </button>
                        </form>
                    </div>

                </div>{{-- /mi-card__body --}}
            </div>{{-- /mi-card --}}

            {{-- ── Footer ── --}}
            <p class="mi-footer">
                نقدر رغبتك في العطاء &nbsp;·&nbsp; نتمنى لك دوام الصحة والعافية
            </p>

        </div>{{-- /mi-center --}}
    </div>{{-- /mi-shell --}}


    <style>
        /* ═══════════════════════════════════════════
           Design Tokens
        ═══════════════════════════════════════════ */
        :root {
            --mi-red-50: #fff1f2;
            --mi-red-100: #ffe4e6;
            --mi-red-200: #fecdd3;
            --mi-red-400: #fb7185;
            --mi-red-500: #f43f5e;
            --mi-red-600: #e11d48;
            --mi-red-700: #be123c;
            --mi-red-900: #881337;

            --mi-neutral-50: #f9fafb;
            --mi-neutral-100: #f3f4f6;
            --mi-neutral-200: #e5e7eb;
            --mi-neutral-400: #9ca3af;
            --mi-neutral-500: #6b7280;
            --mi-neutral-600: #4b5563;
            --mi-neutral-700: #374151;
            --mi-neutral-800: #1f2937;
            --mi-neutral-900: #111827;

            --mi-surface: #ffffff;
            --mi-surface-2: #fafafa;
            --mi-border: #f0f0f0;
            --mi-shadow-sm: 0 1px 3px rgba(0, 0, 0, .07), 0 1px 2px rgba(0, 0, 0, .05);
            --mi-shadow-md: 0 4px 16px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
            --mi-shadow-lg: 0 20px 48px rgba(0, 0, 0, .10), 0 8px 16px rgba(0, 0, 0, .06);
            --mi-radius-card: 1.5rem;
            --mi-radius-inner: 0.875rem;
            --mi-radius-pill: 9999px;
            --mi-font-display: 'Cairo', 'Tajawal', system-ui, sans-serif;
            --mi-font-body: 'Tajawal', 'Cairo', system-ui, sans-serif;
            --mi-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark {
            --mi-surface: #18181b;
            --mi-surface-2: #1f1f23;
            --mi-border: #27272a;
            --mi-neutral-50: #27272a;
            --mi-neutral-100: #3f3f46;
            --mi-neutral-200: #52525b;
            --mi-neutral-600: #a1a1aa;
            --mi-neutral-700: #d4d4d8;
            --mi-neutral-900: #fafafa;
            --mi-shadow-lg: 0 20px 48px rgba(0, 0, 0, .4);
        }

        /* ═══════════════════════════════════════════
           Shell & Layout
        ═══════════════════════════════════════════ */
        .mi-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
            font-family: var(--mi-font-body);
            background: var(--mi-neutral-50);
            position: relative;
            overflow: hidden;
            padding: 2rem 1rem;
        }

        /* Ambient blobs */
        .mi-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            pointer-events: none;
            animation: mi-float 8s ease-in-out infinite alternate;
        }

        .mi-blob--1 {
            width: 480px;
            height: 480px;
            top: -160px;
            left: -160px;
            background: radial-gradient(circle, var(--mi-red-200), transparent 70%);
        }

        .mi-blob--2 {
            width: 360px;
            height: 360px;
            bottom: -120px;
            right: -80px;
            background: radial-gradient(circle, #fda4af, transparent 70%);
            animation-delay: -4s;
        }

        @keyframes mi-float {
            from {
                transform: translate(0, 0) scale(1);
            }

            to {
                transform: translate(20px, 20px) scale(1.05);
            }
        }

        .dark .mi-blob--1 {
            opacity: 0.12;
        }

        .dark .mi-blob--2 {
            opacity: 0.08;
        }

        .mi-center {
            width: 100%;
            max-width: 34rem;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.25rem;
            animation: mi-enter 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes mi-enter {
            from {
                opacity: 0;
                transform: translateY(22px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ═══════════════════════════════════════════
           Brand Bar
        ═══════════════════════════════════════════ */
        .mi-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--mi-red-600);
            font-family: var(--mi-font-display);
            font-size: 1.125rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .mi-brand__drop {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mi-brand__drop svg {
            width: 100%;
            height: 100%;
        }

        .mi-brand__name {
            font-family: var(--mi-font-display);
        }

        /* ═══════════════════════════════════════════
           Card
        ═══════════════════════════════════════════ */
        .mi-card {
            width: 100%;
            background: var(--mi-surface);
            border-radius: var(--mi-radius-card);
            border: 1px solid var(--mi-border);
            box-shadow: var(--mi-shadow-lg);
            overflow: hidden;
        }

        /* Top danger stripe */
        .mi-card__stripe {
            height: 4px;
            background: linear-gradient(90deg,
                    var(--mi-red-400) 0%,
                    var(--mi-red-600) 50%,
                    var(--mi-red-400) 100%);
            background-size: 200% 100%;
            animation: mi-stripe-move 3s linear infinite;
        }

        @keyframes mi-stripe-move {
            from {
                background-position: 0% 0%;
            }

            to {
                background-position: 200% 0%;
            }
        }

        .mi-card__body {
            padding: 2rem 2rem 2.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
        }

        /* ═══════════════════════════════════════════
           Icon Badge
        ═══════════════════════════════════════════ */
        .mi-icon-badge {
            position: relative;
            width: 6rem;
            height: 6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .mi-icon-badge__ring {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid var(--mi-red-200);
        }

        .dark .mi-icon-badge__ring {
            border-color: rgba(190, 18, 60, .25);
        }

        .mi-icon-badge__ring--outer {
            inset: 0;
            animation: mi-pulse-ring 2.4s ease-in-out infinite;
        }

        .mi-icon-badge__ring--inner {
            inset: 8px;
            animation: mi-pulse-ring 2.4s ease-in-out infinite 0.4s;
        }

        @keyframes mi-pulse-ring {

            0%,
            100% {
                opacity: 0.4;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.04);
            }
        }

        .mi-icon-badge__core {
            position: relative;
            z-index: 1;
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--mi-red-50), var(--mi-red-100));
            border: 1.5px solid var(--mi-red-200);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dark .mi-icon-badge__core {
            background: rgba(190, 18, 60, .15);
            border-color: rgba(190, 18, 60, .3);
        }

        .mi-icon-badge__svg {
            width: 2rem;
            height: 2rem;
            color: var(--mi-red-600);
        }

        /* ═══════════════════════════════════════════
           Title Block
        ═══════════════════════════════════════════ */
        .mi-title-block {
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .mi-eyebrow {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--mi-red-500);
            margin-bottom: 0.4rem;
        }

        .mi-title {
            font-family: var(--mi-font-display);
            font-size: 1.625rem;
            font-weight: 800;
            color: var(--mi-neutral-900);
            line-height: 1.2;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .dark .mi-title {
            color: #fafafa;
        }

        /* ─── Status Pill ─── */
        .mi-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.375rem 1rem;
            border-radius: var(--mi-radius-pill);
            background: var(--mi-red-50);
            border: 1px solid var(--mi-red-200);
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--mi-red-700);
        }

        .dark .mi-status-pill {
            background: rgba(190, 18, 60, .12);
            border-color: rgba(190, 18, 60, .3);
            color: #fda4af;
        }

        .mi-status-pill__dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
            background: var(--mi-red-500);
            animation: mi-dot-blink 1.8s ease-in-out infinite;
        }

        @keyframes mi-dot-blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.35;
            }
        }

        /* ═══════════════════════════════════════════
           Description
        ═══════════════════════════════════════════ */
        .mi-desc {
            text-align: center;
            color: var(--mi-neutral-500);
            font-size: 0.9rem;
            line-height: 1.75;
            margin-bottom: 1.5rem;
            max-width: 28rem;
        }

        .dark .mi-desc {
            color: var(--mi-neutral-400);
        }

        /* ═══════════════════════════════════════════
           Detail Panel
        ═══════════════════════════════════════════ */
        .mi-detail-panel {
            width: 100%;
            border-radius: var(--mi-radius-inner);
            border: 1px solid var(--mi-border);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .mi-detail-panel__header {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            background: var(--mi-surface-2);
            border-bottom: 1px solid var(--mi-border);
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--mi-neutral-600);
        }

        .dark .mi-detail-panel__header {
            background: rgba(255, 255, 255, .03);
            color: var(--mi-neutral-400);
        }

        .mi-detail-panel__header-icon {
            width: 1rem;
            height: 1rem;
            color: var(--mi-red-500);
            flex-shrink: 0;
        }

        .mi-detail-panel__rows {
            display: flex;
            flex-direction: column;
        }

        /* ─── Individual Row ─── */
        .mi-detail-row {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--mi-border);
            transition: background var(--mi-transition);
        }

        .mi-detail-row:last-child {
            border-bottom: none;
        }

        .mi-detail-row--reason {
            background: rgba(244, 63, 94, 0.03);
        }

        .dark .mi-detail-row--reason {
            background: rgba(190, 18, 60, .07);
        }

        .mi-detail-row__icon-wrap {
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            background: var(--mi-neutral-50);
            border: 1px solid var(--mi-border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .dark .mi-detail-row__icon-wrap {
            background: rgba(255, 255, 255, .04);
        }

        .mi-detail-row__icon-wrap--danger {
            background: var(--mi-red-50);
            border-color: var(--mi-red-200);
        }

        .dark .mi-detail-row__icon-wrap--danger {
            background: rgba(190, 18, 60, .12);
            border-color: rgba(190, 18, 60, .25);
        }

        .mi-detail-row__icon {
            width: 1rem;
            height: 1rem;
            color: var(--mi-neutral-500);
        }

        .mi-detail-row__icon--danger {
            color: var(--mi-red-500);
        }

        .mi-detail-row__content {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            flex: 1;
            text-align: right;
        }

        .mi-detail-row__label {
            font-size: 0.75rem;
            color: var(--mi-neutral-400);
            font-weight: 500;
        }

        .mi-detail-row__value {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--mi-neutral-800);
        }

        .dark .mi-detail-row__value {
            color: #e4e4e7;
        }

        .mi-detail-row__value--danger {
            color: var(--mi-red-700);
        }

        .dark .mi-detail-row__value--danger {
            color: #fda4af;
        }

        /* ═══════════════════════════════════════════
           Notice Bar
        ═══════════════════════════════════════════ */
        .mi-notice {
            width: 100%;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.875rem 1rem;
            border-radius: var(--mi-radius-inner);
            background: #fffbeb;
            border: 1px solid #fde68a;
            margin-bottom: 1.75rem;
        }

        .dark .mi-notice {
            background: rgba(245, 158, 11, .07);
            border-color: rgba(245, 158, 11, .2);
        }

        .mi-notice__icon {
            width: 1.25rem;
            height: 1.25rem;
            color: #d97706;
            flex-shrink: 0;
            margin-top: 0.05rem;
        }

        .mi-notice__text {
            font-size: 0.8125rem;
            line-height: 1.6;
            color: #92400e;
        }

        .dark .mi-notice__text {
            color: #fcd34d;
        }

        /* ═══════════════════════════════════════════
           Action Buttons
        ═══════════════════════════════════════════ */
        .mi-actions {
            display: flex;
            gap: 0.75rem;
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
        }

        .mi-logout-form {
            display: contents;
        }

        .mi-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            font-family: var(--mi-font-body);
            cursor: pointer;
            transition: all var(--mi-transition);
            border: 1px solid transparent;
            text-decoration: none;
            white-space: nowrap;
        }

        .mi-btn__icon {
            width: 1.1rem;
            height: 1.1rem;
            flex-shrink: 0;
        }

        /* Secondary (gray) */
        .mi-btn--secondary {
            background: var(--mi-neutral-100);
            color: var(--mi-neutral-700);
            border-color: var(--mi-neutral-200);
        }

        .mi-btn--secondary:hover {
            background: var(--mi-neutral-200);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }

        .dark .mi-btn--secondary {
            background: rgba(255, 255, 255, .06);
            color: #d4d4d8;
            border-color: rgba(255, 255, 255, .1);
        }

        .dark .mi-btn--secondary:hover {
            background: rgba(255, 255, 255, .1);
        }

        /* Ghost (red outline) */
        .mi-btn--ghost {
            background: transparent;
            color: var(--mi-red-600);
            border-color: var(--mi-red-300);
        }

        .mi-btn--ghost:hover {
            background: var(--mi-red-50);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(225, 29, 72, .1);
        }

        .dark .mi-btn--ghost {
            color: #fda4af;
            border-color: rgba(190, 18, 60, .4);
        }

        .dark .mi-btn--ghost:hover {
            background: rgba(190, 18, 60, .1);
        }

        /* ═══════════════════════════════════════════
           Footer
        ═══════════════════════════════════════════ */
        .mi-footer {
            font-size: 0.8125rem;
            color: var(--mi-neutral-400);
            font-style: italic;
            text-align: center;
            letter-spacing: 0.01em;
        }

        /* ═══════════════════════════════════════════
           Responsive
        ═══════════════════════════════════════════ */
        @media (max-width: 480px) {
            .mi-card__body {
                padding: 1.5rem 1.25rem 1.75rem;
            }

            .mi-actions {
                flex-direction: column;
            }

            .mi-btn {
                justify-content: center;
            }
        }
    </style>
</x-filament-panels::page>
