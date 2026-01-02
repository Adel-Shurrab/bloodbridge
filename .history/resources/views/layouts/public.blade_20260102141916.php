<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="{{ $description ?? 'BloodBridge - نظام ذكي يربط المتبرعين بالمحتاجين. تبرع بالدم وأنقذ الأرواح اليوم.' }}" />
    <meta name="keywords" content="التبرع بالدم, جسر الدم, نقل الدم, المتبرعين, المستشفيات" />
    <meta name="author" content="BloodBridge" />
    <meta name="theme-color" content="#DC143C" />
    <meta property="og:title" content="{{ $title ?? 'BloodBridge - نظام التبرع بالدم' }}" />
    <meta property="og:description" content="{{ $description ?? 'نظام ذكي يربط المتبرعين بالمحتاجين' }}" />
    <meta property="og:type" content="website" />

    <title>{{ $title ?? 'BloodBridge - إنقاذ الأرواح قطرة قطرة' }}</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('assets/images/logo.jpg') }}" />
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('assets/images/logo.jpg') }}" />

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('assets/styles/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/styles/layout/navbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/styles/layout/footer.css') }}" />

    @vite(['resources/js/app.js'])
    @stack('styles')
</head>

<body>
    <div class="overlay" id="overlay"></div>

    <x-navbar />

    <main>
        {{ $slot }}
    </main>

    <x-footer />
    <x-privacy-modal />

    <script src="{{ asset('assets/scripts/pages/index.js') }}"></script>

    @stack('scripts')
</body>

</html>