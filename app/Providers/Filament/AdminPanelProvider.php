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
use Filament\Navigation\MenuItem;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\CheckForMaintenanceMode;
use App\Settings\GeneralSettings;
use App\Filament\Admin\Widgets;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Cache settings instance to avoid multiple app() resolutions
        $settings = app(GeneralSettings::class);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName(fn() => $settings->getTranslation('site_name'))
            ->brandLogo(fn() => view('filament.logo', ['height' => '3.5rem']))
            ->brandLogoHeight('3.5rem')
            ->homeUrl(fn() => route('home'))
            ->favicon(fn() => $settings->site_favicon ? Storage::url($settings->site_favicon) : asset('assets/images/logo.png'))
            ->font('Cairo')
            ->colors([
                'primary' => Color::Red,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn() => __('Back to Home'))
                    ->url(fn() => route('home'))
                    ->icon('heroicon-o-home'),
            ])
            ->plugin(SpatieTranslatablePlugin::make()->defaultLocales(['ar', 'en']))
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                Widgets\DashboardHeaderWidget::class,
                Widgets\StatsOverview::class,
                Widgets\PendingOrganizationsWidget::class,
            ])
            ->databaseNotifications()
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
                CheckForMaintenanceMode::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
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
        // Manually register widgets as Livewire components to make them available
        // for the Statistics page without adding them to the Dashboard
        \Livewire\Livewire::component('app.filament.admin.widgets.advanced-stats-overview', Widgets\AdvancedStatsOverview::class);
        \Livewire\Livewire::component('app.filament.admin.widgets.blood-type-demand-widget', Widgets\BloodTypeDemandWidget::class);
        \Livewire\Livewire::component('app.filament.admin.widgets.engagement-chart-widget', Widgets\EngagementChartWidget::class);
        \Livewire\Livewire::component('app.filament.admin.widgets.recent-activity-widget', Widgets\RecentActivityWidget::class);
    }
}
