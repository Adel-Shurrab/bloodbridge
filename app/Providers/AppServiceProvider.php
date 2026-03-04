<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
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
    }
}
