<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Gestión de Almacenes e Inventarios
            'warehouse.view',
            'warehouse.create',
            'warehouse.update',
            'warehouse.delete',
            'warehouse.transfer',
            'warehouse.stock-alert',
            'warehouse.import',

            // Kardex y movimientos
            'kardex.view',
            'movement.create',
            'movement.view',
            'movement.update',
            'movement.delete',
            'transfer.create',
            'transfer.view',
            'transfer.update',
            'transfer.delete',

            // Sistema Bancario y Financiero
            'bank-account.view',
            'bank-account.create',
            'bank-account.update',
            'bank-account.delete',
            'bank-account.import',
            'bank-transaction.view',
            'bank-transaction.create',
            'bank-transaction.update',
            'bank-transaction.delete',
            'expense-category.view',
            'expense-category.create',
            'expense-category.update',
            'expense-category.delete',
            'expense-category.import',
            'sales.payment.view',
            'sales.payment.create',
            'sales.payment.update',
            'sales.payment.delete',
            'financial-report.view',
            'financial-report.export',

            // Gestión de Productos y Categorías
            'category.view',
            'category.create',
            'category.update',
            'category.delete',
            'category.import',
            'product.view',
            'product.create',
            'product.update',
            'product.delete',
            'product.import',
            // Servicios (separados de productos inventariables)
            'service.view',
            'service.create',
            'service.update',
            'service.delete',
            'service.import',
            'customer.import',

            // Clientes, Ventas y Facturación
            'customer.view',
            'customer.create',
            'customer.update',
            'customer.delete',
            'quote.view',
            'quote.create',
            'quote.update',
            'quote.delete',
            'quote.import',
            'sale.view',
            'sale.create',
            'sale.update',
            'sale.delete',
            'sale.import',
            'invoice.generate',
            'invoice.view',
            'invoice.delete',
            'sales.history.view',
            'quote.public.view',

            // Compras y Proveedores
            'purchase-order.view',
            'purchase-order.create',
            'purchase-order.update',
            'purchase-order.delete',
            'purchase-order.import',
            'purchase.view',
            'purchase.create',
            'purchase.update',
            'purchase.delete',
            'purchase.import',
            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',
            'supplier.import',

            // Gestión de Usuarios y Roles
            
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'role.view',
            'role.create',
            'role.update',
            'role.delete',
            'permission.view',
            'permission.create',
            'permission.update',
            'permission.delete',
            '2fa.manage',

            // Órdenes de Trabajo y Checkins
            'work-order.view',
            'work-order.create',
            'work-order.update',
            'work-order.delete',
            'work-order.import',
            'checkin.create',
            'checkin.view',

            // Reportes y Exportaciones
            'report.top-products.view',
            'report.top-customers.view',
            'report.low-stock.view',
            'report.export.excel',
            'report.export.pdf',
            'dashboard.view',
            // Otros permisos generales
            'image.upload',
            'image.delete',
        ];

        // 1) Crear permisos (evita duplicados)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2) Crear roles y asignar permisos

        // Admin: todos los permisos
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Técnico: permisos limitados
        $technician = Role::firstOrCreate(['name' => 'technician']);
        $technician->syncPermissions([
            'checkin.create',
            'checkin.view',
            'work-order.update',
            'work-order.view',
            'dashboard.view',
        ]);

        // Cliente: solo vista pública de cotizaciones
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->syncPermissions([
            'quote.public.view',
        ]);

        // 3) Crear/asegurar un usuario de ejemplo como admin (idempotente)
        $demoAdmin = User::firstOrCreate(
            ['email' => 'diegobeteta@distribuidorajadi.com'],
            [
                'name'     => 'Diego Beteta',
                'password' => bcrypt('Gama5649'),
            ]
        );
        $demoAdmin->assignRole($admin);
    }
}
