<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Services\MenuT\MenuBuilder;
use Illuminate\Support\Facades\Auth;

class MenuTComposer
{
    /**
     * Injecta $links en la vista del sidebar.
     */
    public function compose(View $view): void
    {
                $user = Auth::user();
                $isAdmin = $user && method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;

        $links = MenuBuilder::make()
            // PRINCIPAL
            ->header('Principal')
            ->link('Dashboard', 'fa-solid fa-gauge', 'dashboard')

            // TÉCNICOS
            ->header('Técnicos')
            ->group('Técnicos', 'fa-solid fa-tools', function($b) use ($isAdmin) {
                                // Enlaces: siempre apuntan a un index válido
                                // Técnicos no-admin: dirigir a su dashboard operativo
                                $b->link('Órdenes',  'fa-solid fa-clipboard-list', $isAdmin ? 'admin.work-orders.index' : 'dashboard')
                                    ->link('Gastos',   'fa-solid fa-receipt',        $isAdmin ? 'admin.expenses.index' : 'expenses.create');
            })

            // ¡OJO! build() **fuera** del callback
            ->build();

                // Añade "Mapa" sólo para administradores (o usuarios con rol admin)
        if ($isAdmin) {
            // Agregar Mapa con route name al final
            $extra = MenuBuilder::make()->link('Mapa', 'fa-solid fa-map', 'admin.technicians.map')->build();
            $links = array_merge($links, $extra);
        }

        $view->with('links', $links);
    }
}
