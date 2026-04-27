<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('demo-auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('critical-actions', function (Request $request) {
            $userId = optional($request->user())->id ?? 'guest';

            return Limit::perMinute(30)->by($userId.'|'.$request->ip());
        });

        // HTTPS orqali joylashtirilganda URL generatsiyasi to'g'ri bo'lishi uchun
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Use the project's themed paginator everywhere by default — keeps every
        // ->links() output aligned with the design system without per-view tweaks.
        Paginator::defaultView('vendor.pagination.att');
        Paginator::defaultSimpleView('vendor.pagination.att');
    }
}
