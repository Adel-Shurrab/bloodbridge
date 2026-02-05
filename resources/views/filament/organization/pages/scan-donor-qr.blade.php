<x-filament-panels::page>
    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/20">
                <x-heroicon-o-qr-code class="h-6 w-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    تحقق من حضور المتبرع
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    امسح رمز QR للمتبرع للتحقق من الحضور وتسجيل التبرع
                </p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Scanner Section - Takes 2 columns on large screens --}}
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-camera class="h-5 w-5 text-gray-400" />
                        الماسح الضوئي
                    </div>
                </x-slot>

                <x-slot name="description">
                    وجّه الكاميرا نحو رمز QR الخاص بالمتبرع
                </x-slot>

                <div class="space-y-4">
                    {{-- Status Badge --}}
                    <div class="flex justify-center">
                        <div id="scan-status-badge"
                            class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/30">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span id="scan-status-text">جاري تشغيل الكاميرا...</span>
                        </div>
                    </div>

                    {{-- QR Reader Container --}}
                    <div class="relative" wire:ignore>
                        <div id="reader"
                            class="overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800">
                            {{-- Scanner will be injected here by JavaScript --}}
                        </div>

                        {{-- Decorative scanning corners --}}
                        <div class="pointer-events-none absolute inset-0 rounded-xl">
                            <div
                                class="absolute left-2 top-2 h-12 w-12 border-l-4 border-t-4 border-primary-500 rounded-tl-xl">
                            </div>
                            <div
                                class="absolute right-2 top-2 h-12 w-12 border-r-4 border-t-4 border-primary-500 rounded-tr-xl">
                            </div>
                            <div
                                class="absolute bottom-2 left-2 h-12 w-12 border-b-4 border-l-4 border-primary-500 rounded-bl-xl">
                            </div>
                            <div
                                class="absolute bottom-2 right-2 h-12 w-12 border-b-4 border-r-4 border-primary-500 rounded-br-xl">
                            </div>
                        </div>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div class="rounded-lg bg-gray-50 p-3 text-center dark:bg-gray-800/50">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">عمليات المسح اليوم</div>
                            <div id="stat-total" class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">0</div>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3 text-center dark:bg-green-900/20">
                            <div class="text-xs font-medium text-green-600 dark:text-green-400">التحققات الناجحة</div>
                            <div id="stat-success" class="mt-1 text-2xl font-bold text-green-700 dark:text-green-400">0
                            </div>
                        </div>
                        <div class="rounded-lg bg-red-50 p-3 text-center dark:bg-red-900/20">
                            <div class="text-xs font-medium text-red-600 dark:text-red-400">التحققات الفاشلة</div>
                            <div id="stat-failure" class="mt-1 text-2xl font-bold text-red-700 dark:text-red-400">0
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Side Panel - Manual Input & Instructions --}}
        <div class="space-y-6">
            {{-- Manual Input Section --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-pencil-square class="h-5 w-5 text-gray-400" />
                        إدخال يدوي
                    </div>
                </x-slot>

                <x-slot name="description">
                    في حال تعذر المسح الضوئي
                </x-slot>

                <form wire:submit="verifyQRCode" class="space-y-4">
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" wire:model="scannedCode" id="manual-input"
                                placeholder="ألصق الكود هنا أو اكتبه يدوياً..." />
                        </x-filament::input.wrapper>
                    </div>

                    <x-filament::button type="submit" size="lg" class="w-full">
                        <x-heroicon-m-check-circle class="h-5 w-5 ml-2" />
                        تحقق من الكود
                    </x-filament::button>
                </form>
            </x-filament::section>

            {{-- Instructions Card --}}
            <x-filament::section
                class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-light-bulb class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        <span class="text-blue-900 dark:text-blue-100">تعليمات الاستخدام</span>
                    </div>
                </x-slot>

                <div class="space-y-3">
                    <div class="flex gap-3">
                        <div
                            class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                            1
                        </div>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            اسمح للمتصفح بالوصول إلى الكاميرا عند الطلب
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <div
                            class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                            2
                        </div>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            وجّه الكاميرا نحو رمز QR الموجود على بطاقة المتبرع
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <div
                            class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                            3
                        </div>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            انتظر التحقق التلقائي - لا حاجة للنقر على أي زر
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <div
                            class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                            4
                        </div>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            تأكد من وجود إضاءة كافية للحصول على أفضل النتائج
                        </p>
                    </div>
                </div>

                <x-slot name="footerActions">
                    <x-filament::link href="#" color="primary" icon="heroicon-m-question-mark-circle">
                        تواجه مشكلة؟ اطلب المساعدة
                    </x-filament::link>
                </x-slot>
            </x-filament::section>

            {{-- Keyboard Shortcuts Hint --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-2 flex items-center gap-2">
                    <x-heroicon-o-command-line class="h-4 w-4 text-gray-400" />
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        اختصارات لوحة المفاتيح
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">التركيز على الإدخال اليدوي</span>
                        <kbd
                            class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            Ctrl + K
                        </kbd>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">تبديل الكاميرا</span>
                        <kbd
                            class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            Ctrl + C
                        </kbd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                let scanCount = 0;
                let successCount = 0;
                let failureCount = 0;
                let isProcessing = false;

                // Update status badge
                function updateStatus(message, type = 'loading') {
                    const badge = document.getElementById('scan-status-badge');
                    const text = document.getElementById('scan-status-text');

                    // Reset classes
                    badge.className = 'inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium ring-1 ring-inset transition-all duration-300';

                    // Add appropriate classes based on type
                    const classes = {
                        success: 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/30',
                        error: 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/30',
                        ready: 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30',
                        loading: 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/30'
                    };

                    badge.className += ' ' + classes[type];
                    text.textContent = message;
                }

                // Update statistics
                function updateStats() {
                    document.getElementById('stat-total').textContent = scanCount;
                    document.getElementById('stat-success').textContent = successCount;
                    document.getElementById('stat-failure').textContent = failureCount;
                }

                let html5QrCode = null;

                // Core scanning function using Html5Qrcode API (not Scanner Widget)
                async function startScanning() {
                    try {
                        // 1. Get available cameras
                        const devices = await Html5Qrcode.getCameras();
                        if (!devices || devices.length === 0) {
                            updateStatus('❌ لم يتم العثور على كاميرا', 'error');
                            return;
                        }

                        // 2. Select camera (prefer back/environment)
                        const cameraId = selectBestCamera(devices);

                        // 3. Initialize Scanner
                        html5QrCode = new Html5Qrcode("reader");

                        // 4. Start Scanning
                        await html5QrCode.start(
                            cameraId,
                            {
                                fps: 10,    // Scans per second
                                qrbox: { width: 250, height: 250 },
                                aspectRatio: 1.0
                            },
                            onScanSuccess,
                            onScanFailure
                        );

                        updateStatus('📷 الكاميرا جاهزة - ابدأ بالمسح', 'ready');

                    } catch (err) {
                        console.error('Error starting scanner:', err);
                        updateStatus('❌ فشل تشغيل الكاميرا: ' + err, 'error');
                    }
                }

                function selectBestCamera(devices) {
                    // Try to find a camera labeled "back" or "environment"
                    const backCamera = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('environment')
                    );

                    return backCamera ? backCamera.id : devices[0].id;
                }

                function onScanSuccess(decodedText, decodedResult) {
                    if (isProcessing) return;

                    console.log(`QR Code Scanned: ${decodedText}`);
                    isProcessing = true;
                    scanCount++;

                    updateStatus('🔄 جاري التحقق من الكود...', 'loading');
                    updateStats();

                    // Trigger Livewire method
                    @this.verifyQRCode(decodedText)
                        .then((result) => {
                            if (result === true) {
                                // SUCCESS
                                successCount++;
                                updateStatus('✅ تم التحقق بنجاح!', 'success');
                                updateStats();

                                try {
                                    const audio = new Audio(
                                        'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGGa78OScTgwOUKXi8LZjHAU7k9r0y3krBSl+zPLaizsKElyx6OyrWBELTKXh8bllHgU1ivDu0oU2Bx5ruvDmnU0ME1Cl4/C2YhwGPJHX8sx5KwUmecnw3I8+ChVbsOnnqVUSC0yl4fC5ZB4FNYzx79KFNgcdarvv5Z5ODhNPpePwtmIcBT2Q2PTLeSkEKn3N8di8OwoVWbDp56lUEgxLpeDwuWQeBDWM8u/ShTYHHWq77+WeTg4TT6Xj8LZiHAU9kNj0y3kpBCp9zfHYvDsKFVmw6eepVBIMS6Xg8LlkHgQ1jPLv0oU2Bx1qu+/lnk4OE0+l4/C2YhwFPZDY9Mt5KQQqfc3x2Lw7ChVZsOnnqVQSDEul4PC5ZB4ENYzy79KFNgcdarvv5Z5ODhNPpePwtmIcBT2Q2PTLeSkEKn3N8di8OwoVWbDp56lUEgxLpeDwuWQeBDWM8u/Sh'
                                    );
                                    audio.play();
                                } catch (e) { console.log(e); }

                                setTimeout(() => {
                                    updateStatus('📷 جاهز للمسح التالي', 'ready');
                                    isProcessing = false;
                                }, 2000); // 2s delay before next scan
                            } else {
                                // FAILURE
                                failureCount++;
                                updateStatus('❌ الكود غير صالح أو مستخدم', 'error');
                                updateStats();

                                try {
                                    const context = new (window.AudioContext || window.webkitAudioContext)();
                                    const oscillator = context.createOscillator();
                                    oscillator.type = 'sawtooth';
                                    oscillator.frequency.setValueAtTime(150, context.currentTime);
                                    oscillator.connect(context.destination);
                                    oscillator.start();
                                    oscillator.stop(context.currentTime + 0.3);
                                } catch (e) { console.log(e); }

                                setTimeout(() => {
                                    updateStatus('📷 جاهز للمسح التالي', 'ready');
                                    isProcessing = false;
                                }, 4000); // 4s delay for error to allow moving away
                            }
                        })
                        .catch((error) => {
                            console.error(error);
                            isProcessing = false;
                            updateStatus('❌ خطأ في الاتصال', 'error');
                        });
                }

                function onScanFailure(error) {
                    // Ignore frame scan errors (too common)
                }

                // Start the scanner
                startScanning();

                // Clean up on page unload
                window.addEventListener('beforeunload', () => {
                    if (html5QrCode) {
                        html5QrCode.stop().catch(err => console.log('Stop failed', err));
                    }
                });
            });
        </script>

        <style>
            /* QR Scanner Container */
            #reader {
                min-height: 400px;
                position: relative;
                background-color: black;
            }

            /* Video element styling */
            #reader video {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                display: block !important;
            }

            /* Canvas overlay */
            #reader canvas {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                display: none !important;
            }

            /* Hide the library's UI clutter */
            #reader__scan_region {
                background: transparent !important;
            }

            #reader__dashboard_section_csr span,
            #reader__dashboard_section_swaplink {
                display: none !important;
            }

            #reader__dashboard_section {
                position: absolute;
                bottom: 10px;
                left: 0;
                right: 0;
                z-index: 50;
                background: rgba(0, 0, 0, 0.5) !important;
                backdrop-filter: blur(4px);
                border-radius: 12px;
                margin: 0 16px;
                padding: 8px !important;
                justify-content: center;
                gap: 10px;
            }

            /* Camera selection and buttons */
            #reader__dashboard_section_csr button,
            #reader__dashboard_section_fsr button {
                background-color: rgba(255, 255, 255, 0.9) !important;
                border: none !important;
                padding: 6px 12px !important;
                border-radius: 6px !important;
                font-weight: 500 !important;
                font-size: 0.8rem !important;
                color: #000 !important;
                cursor: pointer !important;
            }

            /* Hide status text and header */
            #reader__status_span,
            #reader__header_message {
                display: none !important;
            }

            /* Pulse animation for corners */
            @keyframes pulse-corner {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }
        </style>
    @endpush
</x-filament-panels::page>