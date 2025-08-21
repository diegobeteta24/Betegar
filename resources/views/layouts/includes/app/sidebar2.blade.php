{{-- resources/views/layouts/includes/admin/sidebar.blade.php --}}
@php
    use Illuminate\Support\Str;

    $links = [

        // PRINCIPAL
        ['header'  => 'Principal'],
        [
            'name'   => 'Dashboard',
            'icon'   => 'fa-solid fa-gauge',
            'href'   => route('admin.dashboard'),
            'active' => request()->routeIs('admin.dashboard'),
        ],

        // CATÁLOGO
        ['header' => 'Catálogo'],
        [
            'name'     => 'Catálogo',
            'icon'     => 'fa-solid fa-tags',
            'href'     => '#',
            'active'   => request()->routeIs('admin.categories.*') || request()->routeIs('admin.products.*') || request()->routeIs('admin.services.*') || request()->routeIs('admin.warehouses.*'),
            'children' => [
                [
                    'name'   => 'Categorías',
                    'icon'   => 'fa-solid fa-list',
                    'href'   => route('admin.categories.index'),
                    'active' => request()->routeIs('admin.categories.*'),
                ],
                [
                    'name'   => 'Productos',
                    'icon'   => 'fa-solid fa-box-open',
                    'href'   => route('admin.products.index'),
                    'active' => request()->routeIs('admin.products.*'),
                ],
                [
                    'name'   => 'Servicios',
                    'icon'   => 'fa-solid fa-concierge-bell',
                    'href'   => route('admin.services.index'),
                    'active' => request()->routeIs('admin.services.*'),
                ],
                [
                    'name'   => 'Almacenes',
                    'icon'   => 'fa-solid fa-warehouse',
                    'href'   => route('admin.warehouses.index'),
                    'active' => request()->routeIs('admin.warehouses.*'),
                ],
            ],
        ],

        // VENTAS
        ['header' => 'Ventas'],
        [
            'name'     => 'Ventas',
            'icon'     => 'fa-solid fa-money-bill-wave',
            'href'     => '#',
            'active'   => request()->routeIs(['admin.customers.*', 'admin.quotes.*', 'admin.sales.*']),
            'children' => [
                [
                    'name'   => 'Clientes',
                    'icon'   => 'fa-solid fa-user-group',
                    'href'   => route('admin.customers.index'),
                    'active' => request()->routeIs('admin.customers.*'),
                ],
                [
                    'name'   => 'Cotizaciones',
                    'icon'   => 'fa-solid fa-file-invoice',
                    'href'   => route('admin.quotes.index'),
                    'active' => request()->routeIs('admin.quotes.*'),
                ],
                [
                    'name'   => 'Ventas',
                    'icon'   => 'fa-solid fa-cash-register',
                    'href'   => route('admin.sales.index'),
                    'active' => request()->routeIs('admin.sales.*'),
                ],
            ],
        ],

        // COMPRAS
        ['header' => 'Compras'],
        [
            'name'     => 'Compras',
            'icon'     => 'fa-solid fa-cart-shopping',
            'href'     => '#',
            'active'   => request()->routeIs(['admin.suppliers.*', 'admin.purchase-orders.*', 'admin.purchases.*']),
            'children' => [
                [
                    'name'   => 'Proveedores',
                    'icon'   => 'fa-solid fa-truck',
                    'href'   => route('admin.suppliers.index'),
                    'active' => request()->routeIs('admin.suppliers.*'),
                ],
                [
                    'name'   => 'Órdenes de compra',
                    'icon'   => 'fa-solid fa-file-contract',
                    'href'   => route('admin.purchase-orders.index'),
                    'active' => request()->routeIs('admin.purchase-orders.*'),
                ],
                [
                    'name'   => 'Compras',
                    'icon'   => 'fa-solid fa-boxes-packing',
                    'href'   => route('admin.purchases.index'),
                    'active' => request()->routeIs('admin.purchases.*'),
                ],
            ],
        ],

        // Ordenes de trabajo
        ['header' => 'Empleados'],
        [
            'name'     => 'Empleados',
            'icon'     => 'fa-solid fa-tools',
            'href'     => '#',
            'active'   => request()->routeIs('admin.work-orders.*'),
            'children' => [
                [
                    'name'   => 'Órdenes',
                    'icon'   => 'fa-solid fa-clipboard-list',
                    'href'   => '',
                    'active' => false,
                ],
                [
                    'name'   => 'Checkins',
                    'icon'   => 'fa-solid fa-check',
                    'href'   => '',
                    'active' => false,
                ],
                [
                    'name'   => 'Mapa',
                    'icon'   => 'fa-solid fa-map',
                    'href'   => '',
                    'active' => false,
                ],
            ],
        ],
        

        // MOVIMIENTOS
        ['header' => 'Movimientos'],
        [
            'name'     => 'Movimientos',
            'icon'     => 'fa-solid fa-exchange-alt',
            'href'     => '#',
            'active'   =>  request()->routeIs(['admin.movements.*', 'admin.transfers.*']),
            'children' => [
                [
                    'name'   => 'Entradas y Salidas',
                    'icon'   => 'fa-solid fa-arrows-turn-to-dots',
                    'href'   => route('admin.movements.index'),
                    'active' => request()->routeIs('admin.movements.*'),
                ],
                //Transfers
                [
                    'name'   => 'Transferencias',
                    'icon'   => 'fa-solid fa-arrows-turn-right',
                    'href'   => route('admin.transfers.index'),
                    'active' => request()->routeIs('admin.transfers.*'),
                ],
               
            ],
        ],

        // REPORTES
        ['header' => 'Reportes'],
        [
            'name'   => 'Reportes',
            'icon'   => 'fa-solid fa-chart-line',
            'active'   =>  request()->routeIs([
                'admin.reports.top-products',
                'admin.reports.top-customers',
                'admin.reports.low-stock',
            ]),
            'children' => [
                [
                    'name'   => 'Productos top',
                    'icon'   => 'fa-solid fa-chart-simple',
                    'href'   => route('admin.reports.top-products'),
                    'active' => request()->routeIs('admin.reports.top-products'),
                ],
                
                [
                    'name'   => 'Mejores clientes',
                    'icon'   => 'fa-solid fa-arrow-up',
                    'href'   => route('admin.reports.top-customers'),
                    'active' => request()->routeIs('admin.reports.top-customers'),
                ],
                [
                    'name'   => 'Bajo stock',
                    'icon'   => 'fa-solid fa-flag',
                    'href'   => route('admin.reports.low-stock'),
                    'active' => request()->routeIs('admin.reports.low-stock'),
                ],
            ],
        ],
        // Banca
        ['header' => 'Banca'],
        [
            'name'   => 'Banca',
            'icon'   => 'fa-solid fa-piggy-bank',
            'href'   => '',
            'active' => false,
        ],

        // CONFIGURACIÓN
        ['header' => 'Configuración'],
        [
            'name'   => 'Usuarios',
            'icon'   => 'fa-solid fa-users',
            'href'   => '',
            'active' => false,
        ],
        [
            'name'   => 'Roles',
            'icon'   => 'fa-solid fa-user-shield',
            'href'   => '',
            'active' => false,
        ],
        [
            'name'   => 'Permisos',
            'icon'   => 'fa-solid fa-lock',
            'href'   => '',
            'active' => false,
        ],
        [
            'name'   => 'Ajustes',
            'icon'   => 'fa-solid fa-cog',
            'href'   => '',
            'active' => false,
        ],

    ];
@endphp

<aside id="logo-sidebar"
       class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full
              bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
       aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            @foreach($links as $link)
                {{-- Header --}}
                @if(isset($link['header']))
                    <li class="mt-6 px-3 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">
                        {{ $link['header'] }}
                    </li>
                    @continue
                @endif

                {{-- Submenú --}}
                @if(isset($link['children']))
                    @php $id = 'submenu-'.Str::slug($link['name']); @endphp
                    <li>
                        <button type="button"
                                class="flex items-center w-full p-2 rounded-lg
                                       {{ $link['active']
                                          ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white'
                                          : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                                data-collapse-toggle="{{ $id }}"
                                aria-controls="{{ $id }}"
                                aria-expanded="{{ $link['active'] ? 'true' : 'false' }}">
                            <span class="w-6 h-6 inline-flex justify-center items-center">
                                <i class="{{ $link['icon'] }}"></i>
                            </span>
                            <span class="flex-1 ml-3 text-left">{{ $link['name'] }}</span>
                            <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 
                                         0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 
                                         0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <ul id="{{ $id }}"
                            class="{{ $link['active'] ? 'block' : 'hidden' }} py-2 space-y-2">
                            @foreach($link['children'] as $child)
                                <li>
                                    <a href="{{ $child['href'] }}"
                                       class="flex items-center w-full pl-11 p-2 rounded-lg
                                              {{ $child['active']
                                                 ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white'
                                                 : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                                        <i class="w-6 h-6 inline-flex justify-center items-center {{ $child['icon'] }}"></i>
                                        <span class="ml-3">{{ $child['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    {{-- Enlace simple --}}
                    <li>
                        <a href="{{ $link['href'] }}"
                           class="flex items-center p-2 rounded-lg
                                  {{ $link['active']
                                     ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white'
                                     : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' }}">
                            <span class="w-6 h-6 inline-flex justify-center items-center">
                                <i class="{{ $link['icon'] }}"></i>
                            </span>
                            <span class="ml-3">{{ $link['name'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</aside>
