<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Filament\Tables\Table;
use Filament\Actions\Action;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Illuminate\Validation\Rules\Password::defaults(function () {
            return \Illuminate\Validation\Rules\Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
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
