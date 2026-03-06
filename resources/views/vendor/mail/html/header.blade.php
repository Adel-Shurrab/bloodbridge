@php
    $settings = app(\App\Settings\GeneralSettings::class);
@endphp
@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <img src="{{ $settings->site_logo ? asset('storage/' . $settings->site_logo) : asset('assets/images/logo.png') }}"
                class="logo" alt="{{ $settings->site_name }} Logo">
        </a>
    </td>
</tr>
