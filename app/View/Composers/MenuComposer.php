<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Services\Menu\MenuBuilder;

class MenuComposer
{
    /**
     * Injecta $links en la vista del sidebar.
     */
    public function compose(View $view): void
    {
        $links = MenuBuilder::make()
            // PRINCIPAL
            ->header('Principal')
            ->link('Dashboard', 'fa-solid fa-gauge', 'admin.dashboard')

            // CATÁLOGO
            ->header('Catálogo')
            ->group('Catálogo', 'fa-solid fa-tags', function($b) {
                $b->link('Categorías', 'fa-solid fa-list',      'admin.categories.index')
                  ->link('Productos',   'fa-solid fa-box-open', 'admin.products.index')
                  ->link('Servicios',   'fa-solid fa-concierge-bell', 'admin.services.index')
                  ->link('Almacenes',   'fa-solid fa-warehouse','admin.warehouses.index');
            })

            // VENTAS
            ->header('Ventas')
            ->group('Ventas', 'fa-solid fa-money-bill-wave', function($b) {
                $b->link('Clientes',       'fa-solid fa-user-group',     'admin.customers.index')
                  ->link('Cotizaciones',   'fa-solid fa-file-invoice',  'admin.quotes.index')
                  ->link('Ventas',         'fa-solid fa-cash-register', 'admin.sales.index')
                  ->link('Recordatorios',  'fa-regular fa-bell',        'admin.crm.reminders.index');
            })

            // COMPRAS
            ->header('Compras')
            ->group('Compras', 'fa-solid fa-cart-shopping', function($b) {
                $b->link('Proveedores',       'fa-solid fa-truck',          'admin.suppliers.index')
                  ->link('Órdenes de compra', 'fa-solid fa-file-contract', 'admin.purchase-orders.index')
                  ->link('Compras',           'fa-solid fa-boxes-packing',  'admin.purchases.index');
            })

            // EMPLEADOS
            ->header('Empleados')
      ->group('Empleados', 'fa-solid fa-tools', function($b) {
        // Enlaces a índices (se reemplaza Checkins por Gastos)
  $b->link('Órdenes',  'fa-solid fa-clipboard-list', 'admin.work-orders.index')
                  ->link('Gastos',   'fa-solid fa-receipt',        'admin.expenses.index')
                  ->link('Mapa',     'fa-solid fa-map',            'admin.technicians.map')
                  ->link('Overview', 'fa-solid fa-hand-holding-dollar', 'admin.technicians.overview');
            })

            // MOVIMIENTOS
            ->header('Movimientos')
            ->group('Movimientos', 'fa-solid fa-exchange-alt', function($b) {
                $b->link('Entradas y Salidas','fa-solid fa-arrows-turn-to-dots','admin.movements.index')
                  ->link('Transferencias',     'fa-solid fa-arrows-turn-right','admin.transfers.index');
            })

            // REPORTES
            ->header('Reportes')
            ->group('Reportes', 'fa-solid fa-chart-line', function($b) {
                $b->link('Productos top',   'fa-solid fa-chart-simple',  'admin.reports.top-products')
                  ->link('Mejores clientes', 'fa-solid fa-arrow-up',      'admin.reports.top-customers')
                  ->link('Bajo stock',       'fa-solid fa-flag',          'admin.reports.low-stock');
            })

            // BANCA
            ->header('Banca')
            ->group('Banca', 'fa-solid fa-piggy-bank', function($b) {
                $b->link('Categorías de gasto',   'fa-solid fa-list',             'admin.expense-categories.index')
                  ->link('Cuentas bancarias',     'fa-solid fa-piggy-bank',       'admin.bank-accounts.index')
                  ->link('Movimientos bancarios', 'fa-solid fa-money-check-dollar','admin.bank-transactions.index')
                  ->link('Pagos de ventas',       'fa-solid fa-hand-holding-dollar','admin.sales.payments.index');
            })

            // CONFIGURACIÓN
            ->header('Configuración')
            ->link('Usuarios', 'fa-solid fa-users',        'admin.users.index')
            ->link('Roles',    'fa-solid fa-user-shield',  'admin.roles.index')
            ->link('Ajustes',  'fa-solid fa-cog',          'admin.settings.index')
            ->build();

        $view->with('links', $links);
    }
}
