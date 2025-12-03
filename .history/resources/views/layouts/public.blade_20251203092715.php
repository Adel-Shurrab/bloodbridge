<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description ?? 'BloodBridge - نظام إدارة التبرع بالدم' }}" />

    <title>{{ $title ?? 'BloodBridge' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('assets/styles/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/styles/layout/navbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/styles/layout/footer.css') }}" />

    @stack('styles')
</head>
<body>
    <div class="overlay" id="overlay"></div>

    <x-navbar />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    <script src="{{ asset('assets/scripts/pages/index.js') }}"></script>

    @stack('scripts')
</body>
</html>