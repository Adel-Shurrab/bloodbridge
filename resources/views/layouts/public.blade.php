<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
    dir="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="{{ $description ?? ($settings->getTranslation('seo_description') ?? __(':site_name - A smart system connecting donors with those in need. Donate blood and save lives today.', ['site_name' => $settings->getTranslation('site_name')])) }}" />
    <meta name="keywords"
        content="{{ $settings->getTranslation('seo_keywords') ?? __('blood donation, blood bridge, blood transfusion, donors, hospitals') }}" />
    <meta name="author" content="{{ $settings->getTranslation('site_name') }}" />
    <meta name="theme-color" content="#DC143C" />
    <meta property="og:title"
        content="{{ $title ?? ($settings->getTranslation('seo_title') ?? __(':site_name - Blood Donation System', ['site_name' => $settings->getTranslation('site_name')])) }}" />
    <meta property="og:description"
        content="{{ $description ?? ($settings->getTranslation('seo_description') ?? __('A smart system connecting donors with those in need')) }}" />
    <meta property="og:type" content="website" />
    <script>
        window.appConfig = {
            locale: "{{ str_replace('_', '-', app()->getLocale()) }}",
            dir: "{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
        };
    </script>

    <title>
        {{ $title ?? ($settings->getTranslation('seo_title') ?? __(':site_name - Saving lives drop by drop', ['site_name' => $settings->getTranslation('site_name')])) }}
    </title>

    <link rel="icon"
        href="{{ $settings->site_favicon ? Storage::url($settings->site_favicon) : asset('assets/images/logo.png') }}" />
    <link rel="shortcut icon"
        href="{{ $settings->site_favicon ? Storage::url($settings->site_favicon) : asset('assets/images/logo.png') }}" />

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
