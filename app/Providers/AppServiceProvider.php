<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\MenuComposer;
use App\View\Composers\MenuTComposer;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // tu binding de Kardex u otros servicios…
        $this->app->bind('kardex', function($app) {
            return new \App\Services\KardexService();
        });
    }

    public function boot(): void
    {
          URL::forceScheme('https');
        
        // Inyecta Menu|Composer en el partial del sidebar
        View::composer(
            'layouts.admin',
            MenuComposer::class
        );

        // Inyecta MenuTComposer en el partial del sidebar
        View::composer(
            'layouts.app',
            MenuTComposer::class
        );
        \App\Models\Purchase::observe(\App\Observers\PurchaseObserver::class);
    }
}
