{{-- resources/views/layouts/admin.blade.php --}}
@props(['breadcrumbs' => []])

@php
    // Determina dinámicamente el título de la página
    $pageTitle = config('app.name', 'Laravel');
    if (count($breadcrumbs)) {
        $pageTitle = $breadcrumbs[array_key_last($breadcrumbs)]['name'];
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/5ed949be3d.js" crossorigin="anonymous"></script>
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Vite: TailwindCSS (con Flowbite) + JS (con Flowbite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

        <wireui:scripts />

    {{-- Estilos de WireUI (se inyectan después de Vite para evitar conflictos) --}}
    {{-- <wireui:styles /> --}}

    
    @livewireStyles
    @stack('css')
    @PwaHead
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- Navigation & Sidebar --}}
    @include('layouts.includes.admin.navigation')
    @include('layouts.includes.admin.sidebar')

    {{-- Main Content --}}
    <div class="p-4 sm:ml-64 ">
        {{-- Header: Breadcrumbs + Title + Action --}}
        <div class="mt-14 flex items-center">
            {{-- Breadcrumbs --}}
            
                @include('layouts.includes.admin.breadcrumb')
            
            @isset($action)
                <div class="ml-auto">
                    {{-- Action Button --}}
                    {{ $action }}
                </div>
            @endisset
        </div>

        {{-- Page Slot --}}
        {{ $slot }}
    </div>

    {{-- Modals --}}
    @stack('modals')

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Vite JS bundle already contains Flowbite behavior --}}
    {{-- WireUI scripts (notificaciones, modals, etc.) --}}


    {{-- Inline SweetAlert delete buttons --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esta acción!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
    <script>
        Livewire.on('swal', (data) => {
            Swal.fire(data[0]);
        });

    </script>

    @stack('js')

    {{-- Alerts --}}
    @include('layouts.includes.admin.alerts')
    @RegisterServiceWorkerScript
</body>
</html>
