<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Columns\ToggleColumn;

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
            config(['app.name' => $settings->site_name]);
        } catch (\Throwable $e) {
        }

        Table::configureUsing(function (Table $table): void {
            $table
                ->filtersTriggerAction(
                    fn(Action $action) => $action
                        ->button()
                        ->label('تصفية'),
                );
        });
    }
}
