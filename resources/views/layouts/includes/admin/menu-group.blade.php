{{-- resources/views/layouts/includes/admin/menu-group.blade.php --}}
@php
    use Illuminate\Support\Str;

    $id = 'submenu-'.Str::slug($title);
    $isActive = collect($children)->contains(function($child) {
        return ! preg_match('/^(\/|#|https?:\/\/)/', $child->getRoute())
            && request()->routeIs($child->getRoute().'*');
    });
@endphp

<button type="button"
        class="flex items-center w-full p-2 rounded-lg
               {{ $isActive
                  ? 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white'
                  : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white' }}"
        data-collapse-toggle="{{ $id }}"
        aria-controls="{{ $id }}"
        aria-expanded="{{ $isActive ? 'true' : 'false' }}">
    <span class="w-6 h-6 inline-flex justify-center items-center">
        <i class="{{ $icon }}"></i>
    </span>
    <span class="flex-1 ml-3 text-left">{{ $title }}</span>
    <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd"
              d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94
                 a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08
                 0l-4.25-4.5a.75.75 0 01.02-1.06z"
              clip-rule="evenodd"></path>
    </svg>
</button>
<ul id="{{ $id }}" class="{{ $isActive ? 'block' : 'hidden' }} py-2 space-y-2">
    @foreach($children as $child)
        <li class="pl-11">{!! $child->render() !!}</li>
    @endforeach
</ul>
