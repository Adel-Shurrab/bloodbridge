<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Navigation\MenuItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\Organization;
use App\Filament\Organization\Pages\EditOrganizationProfile;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Storage;

class OrganizationPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        $settings = app(GeneralSettings::class);

        return $panel
            ->id('organization')
            ->path('org')
            ->viteTheme('resources/css/filament/organization/theme.css')
            ->brandName(fn() => $settings->site_name)
            ->brandLogo(fn() => view('filament.logo', ['height' => '2.5rem']))
            ->brandLogoHeight('2.5rem')
            ->homeUrl(fn() => route('home'))
            ->favicon(fn() => $settings->site_favicon ? Storage::disk('public')->url($settings->site_favicon) : asset('assets/images/logo.png'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('العودة للموقع')
                    ->url(fn() => route('home'))
                    ->icon('heroicon-o-home'),
            ])
            ->discoverResources(in: app_path('Filament/Organization/Resources'), for: 'App\Filament\Organization\Resources')
            ->discoverPages(in: app_path('Filament/Organization/Pages'), for: 'App\Filament\Organization\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Organization/Widgets'), for: 'App\Filament\Organization\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
                \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            ])
            ->tenantMiddleware([
                \App\Http\Middleware\CheckOrganizationApproved::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->tenant(Organization::class, slugAttribute: 'slug')
            ->tenantProfile(EditOrganizationProfile::class)
            ->renderHook(
                \Filament\View\PanelsRenderHook::FOOTER,
                fn() => view('filament.footer'),
            );
    }

    public function boot(): void
    {
        \Filament\Facades\Filament::renderHook(
            \Filament\View\PanelsRenderHook::HEAD_END,
            fn() => new \Illuminate\Support\HtmlString('<style>.fi-logo { background: transparent !important; } img.fi-logo { mix-blend-mode: multiply; } .dark img.fi-logo { mix-blend-mode: normal; filter: brightness(0) invert(1); }</style>')
        );
    }
}
