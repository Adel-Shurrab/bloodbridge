<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Filament\Tables\Table;
use Filament\Actions\Action;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use App\Services\DonorScoringService;
use App\Settings\ScoringSettings;
use App\Services\BloodRequestBroadcastService;
use App\Services\FastApiCircuitBreaker;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Password validation rules
        \Illuminate\Validation\Rules\Password::defaults(function () {
            return \Illuminate\Validation\Rules\Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        $this->app->singleton(
            DonorScoringService::class,
            function ($app) {
                return new DonorScoringService(
                    $app->make(ScoringSettings::class),
                    $app->make(FastApiCircuitBreaker::class),
                );
            }
        );

        $this->app->singleton(
            BloodRequestBroadcastService::class,
            function ($app) {
                return new BloodRequestBroadcastService(
                    $app->make(DonorScoringService::class),
                    $app->make(ScoringSettings::class),
                );
            }
        );

        $this->app->singleton(
            FastApiCircuitBreaker::class,
            function ($app) {
                return new FastApiCircuitBreaker(
                    $app->make(ScoringSettings::class),
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $settings = app(\App\Settings\GeneralSettings::class);
            view()->share('settings', $settings);
            config(['app.name' => (string) $settings->site_name]);

            \Illuminate\Support\Facades\DB::listen(function ($query) {
                if (str_contains(strtolower($query->sql), 'delete from `notifications`') || str_contains(strtolower($query->sql), 'update `notifications`')) {
                    \Illuminate\Support\Facades\Log::info('Notification Query:', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                        'url' => request()->fullUrl(),
                        'method' => request()->method(),
                        'user_id' => auth()->id(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AppServiceProvider: failed to load GeneralSettings', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
        }

        Table::configureUsing(function (Table $table): void {
            $table
                ->filtersTriggerAction(
                    fn(Action $action) => $action
                        ->button()
                        ->label(__('Filter')),
                );
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch->locales(['ar', 'en']);
        });
    }
}
