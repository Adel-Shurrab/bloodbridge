@php
    $settings = app(\App\Settings\GeneralSettings::class);
    $logo = $settings->site_logo
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings->site_logo)
        : asset('assets/images/logo.png');
@endphp

<div class="flex items-center gap-3 whitespace-nowrap" style="display: flex; align-items: center; flex-wrap: nowrap;">
    <img src="{{ $logo }}" alt="{{ $settings->site_name }}"
        style="height: {{ $height ?? '3.5rem' }}; width: auto; object-fit: contain;" class="fi-logo">
    <span class="text-xl font-bold text-gray-900 dark:text-white"
        style="line-height: {{ $height ?? '3.5rem' }};">{{ $settings->site_name }}</span>
</div>
