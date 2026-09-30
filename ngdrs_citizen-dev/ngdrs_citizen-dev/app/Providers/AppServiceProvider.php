<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

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
        $appUrl = config('app.url');
        $parsed = parse_url($appUrl);
        $prefix = isset($parsed['path'])
            ? rtrim($parsed['path'], '/')
            : '';

        if (!empty($prefix)) {
            URL::forceRootUrl($appUrl);

            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }

            if (!$this->app->runningInConsole()) {
                Paginator::currentPathResolver(function () {
                    $request = request();

                    return rtrim(config('app.url'), '/')
                        . $request->getPathInfo();
                });
            }
        }

        // Blade::component('layouts.dashboard', 'dashboard-layout');
        // require_once app_path('Helpers/api_helper.php');
        // require_once app_path('Helpers/jwt_helper.php');
        // require_once app_path('Helpers/response_helper.php');
        // require_once app_path('Helpers/encryption_helper.php');
    }
}
