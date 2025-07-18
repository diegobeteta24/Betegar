<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\KardexService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('kardex', function ($app) {
            return new KardexService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
