<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function(){
            // Agrupamos rutas autenticadas normales
            Route::middleware(['web','auth'])
                ->group(base_path('routes/web.php'));

            // Rutas de administración
            Route::middleware(['web','auth'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enable Sanctum stateful API so session cookies authenticate API requests
        if (method_exists($middleware, 'statefulApi')) {
            $middleware->statefulApi();
        }
        // Registrar alias de middleware 'role' (Spatie) de forma temprana
        if (method_exists($middleware, 'alias')) {
            $middleware->alias([
                'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            ]);
        }
        // Evitar indexación por buscadores en toda la app
        if (method_exists($middleware, 'append')) {
            $middleware->append(\App\Http\Middleware\NoIndex::class);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
