<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Filament\Navigation\MenuItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Storage;

class DonorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = app(GeneralSettings::class);

        return $panel
            ->id('donor')
            ->path('donor')
            ->brandName(fn() => $settings->getTranslation('site_name'))
            ->brandLogo(fn() => view('filament.logo', ['height' => '2.5rem']))
            ->brandLogoHeight('2.5rem')
            ->homeUrl(fn() => route('home'))
            ->favicon(fn() => $settings->site_favicon ? Storage::url($settings->site_favicon) : asset('assets/images/logo.png'))
            ->font('Cairo')
            ->colors([
                'primary' => Color::Red,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn() => \App\Filament\Donor\Pages\EditProfile::getLabel())
                    ->url(fn (): string => \App\Filament\Donor\Pages\EditProfile::getUrl())
                    ->icon('heroicon-m-user-circle'),
                MenuItem::make()
                    ->label(fn() => \App\Filament\Donor\Pages\ChangePassword::getNavigationLabel())
                    ->url(fn (): string => \App\Filament\Donor\Pages\ChangePassword::getUrl())
                    ->icon('heroicon-m-key'),
                MenuItem::make()
                    ->label(fn() => __('Back to Home'))
                    ->url(fn() => route('home'))
                    ->icon('heroicon-o-home'),
            ])
            ->plugin(SpatieTranslatablePlugin::make()->defaultLocales(['ar', 'en']))
            ->discoverResources(in: app_path('Filament/Donor/Resources'), for: 'App\Filament\Donor\Resources')
            ->discoverPages(in: app_path('Filament/Donor/Pages'), for: 'App\Filament\Donor\Pages')
            ->pages([
                \App\Filament\Donor\Pages\Dashboard::class,
                \App\Filament\Donor\Pages\EditProfile::class,
                \App\Filament\Donor\Pages\ChangePassword::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Donor/Widgets'), for: 'App\Filament\Donor\Widgets')
            ->widgets([
                \App\Filament\Donor\Widgets\DonorHeaderWidget::class,
                \App\Filament\Donor\Widgets\DonorStatsOverviewWidget::class,
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
                \App\Http\Middleware\EnsureEmailIsVerifiedUnlessAdmin::class,
                \App\Http\Middleware\CheckDonorIneligibility::class,
            ])
            ->databaseNotifications()
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn() => new \Illuminate\Support\HtmlString(
                    '<meta name="user-id" content="' . auth()->id() . '">' .
                    '<style>.fi-logo { background: transparent !important; } img.fi-logo { mix-blend-mode: multiply; } .dark img.fi-logo { mix-blend-mode: normal; filter: brightness(0) invert(1); }</style>'
                )
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::FOOTER,
                fn() => view('filament.footer'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::SCRIPTS_AFTER,
                fn() => new \Illuminate\Support\HtmlString(
                    '<script data-navigate-once>document.addEventListener("livewire:initialized",function(){Livewire.hook("request",function(e){e.options.headers["X-Livewire"]=!0})})</script>'
                ),
            );
    }

    public function boot(): void
    {
        //
    }
}
