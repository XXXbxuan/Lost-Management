<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('local') && config('app.url')) {
            URL::forceRootUrl(config('app.url'));

            $scheme = parse_url(config('app.url'), PHP_URL_SCHEME);

            if ($scheme) {
                URL::forceScheme($scheme);
            }
        }
    }
}