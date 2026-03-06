<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="{{ $description ?? ($settings->seo_description ?? $settings->site_name . ' - نظام ذكي يربط المتبرعين بالمحتاجين. تبرع بالدم وأنقذ الأرواح اليوم.') }}" />
    <meta name="keywords"
        content="{{ $settings->seo_keywords ?? 'التبرع بالدم, جسر الدم, نقل الدم, المتبرعين, المستشفيات' }}" />
    <meta name="author" content="{{ $settings->site_name }}" />
    <meta name="theme-color" content="#DC143C" />
    <meta property="og:title"
        content="{{ $title ?? ($settings->seo_title ?? $settings->site_name . ' - نظام التبرع بالدم') }}" />
    <meta property="og:description"
        content="{{ $description ?? ($settings->seo_description ?? 'نظام ذكي يربط المتبرعين بالمحتاجين') }}" />
    <meta property="og:type" content="website" />

    <title>{{ $title ?? ($settings->seo_title ?? $settings->site_name . ' - إنقاذ الأرواح قطرة قطرة') }}</title>

    <link rel="icon"
        href="{{ $settings->site_favicon ? Storage::disk('public')->url($settings->site_favicon) : asset('assets/images/logo.png') }}" />
    <link rel="shortcut icon"
        href="{{ $settings->site_favicon ? Storage::disk('public')->url($settings->site_favicon) : asset('assets/images/logo.png') }}" />

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('assets/styles/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/styles/layout/navbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/styles/layout/footer.css') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body x-data>
    <div class="overlay" id="overlay"></div>

    <x-navbar />

    <main>
        {{ $slot }}
    </main>

    <x-footer />
    <x-privacy-modal />
    <x-eligibility-modal />

    <script src="{{ asset('assets/scripts/pages/index.js') }}"></script>

    @stack('scripts')
</body>

</html>
