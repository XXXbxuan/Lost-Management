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
        // ✅ Root cause fix:
        // Force Laravel to generate URLs (route(), url()) using APP_URL
        // Useful for ngrok / cross-network email links in local environment.
        if (app()->environment('local') && config('app.url')) {
            URL::forceRootUrl(config('app.url'));

            $scheme = parse_url(config('app.url'), PHP_URL_SCHEME);
            if ($scheme) {
                URL::forceScheme($scheme);
            }
        }
    }
}