<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS only for ngrok domains or when explicitly requested
        $appUrl = env('APP_URL', '');
        $forceHttps = env('FORCE_HTTPS', false);
        
        if ($forceHttps && (str_contains($appUrl, 'ngrok') || str_contains($appUrl, 'https://'))) {
            URL::forceScheme('https');
        }
    }
}
