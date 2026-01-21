<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DonorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('donor')
            ->path('donor')
            ->brandName(fn(\App\Settings\GeneralSettings $settings) => $settings->site_name)
            ->font('Cairo')
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Donor/Resources'), for: 'App\Filament\Donor\Resources')
            ->discoverPages(in: app_path('Filament/Donor/Pages'), for: 'App\Filament\Donor\Pages')
            ->pages([
                \App\Filament\Donor\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Donor/Widgets'), for: 'App\Filament\Donor\Widgets')
            ->widgets([
                \App\Filament\Donor\Widgets\DonorHeaderWidget::class,
                \App\Filament\Donor\Widgets\DonorStatsOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\CheckForMaintenanceMode::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications(); // Enable notification bell for donors
    }
}
