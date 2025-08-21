{{-- resources/views/layouts/includes/admin/menu-link.blade.php --}}
@php
    // Detecta si es URL “en bruto” (starts with /, # o http)
    $isRawUrl  = preg_match('/^(\/|#|https?:\/\/)/', $route);
    $url       = $isRawUrl ? $route : route($route);
    $isActive  = ! $isRawUrl && request()->routeIs($route);
@endphp

<a href="{{ $url }}"
   class="flex items-center w-full p-2 text-base font-normal rounded-lg
       {{ $isActive
           ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white'
           : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' }}">
    <i class="{{ $icon }} w-6 h-6"></i>
    <span class="flex-1 ml-3">{{ $title }}</span>
</a>
