{{-- resources/views/layouts/app.blade.php --}}
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
    <meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
    @if(env('VAPID_PUBLIC_KEY'))
        <meta name="vapid-public-key" content="{{ env('VAPID_PUBLIC_KEY') }}">
    @endif

    <title>{{ $pageTitle }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/pwa-192.png') }}">
    <link rel="mask-icon" href="{{ asset('logo.png') }}" color="#ef1515">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/5ed949be3d.js" crossorigin="anonymous"></script>
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Vite: TailwindCSS (con Flowbite) + JS (sin iniciar Alpine) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- PRE-LIVEWIRE: Tablas + WireUI (escuchan alpine:init) --}}
    <link rel="stylesheet" href="{{ asset('vendor/rappasoft/livewire-tables/css/laravel-livewire-tables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/rappasoft/livewire-tables/css/laravel-livewire-tables-thirdparty.min.css') }}" />
    <script src="{{ asset('vendor/rappasoft/livewire-tables/js/laravel-livewire-tables.min.js') }}" data-order="pre-livewire"></script>
    <script src="{{ asset('vendor/rappasoft/livewire-tables/js/laravel-livewire-tables-thirdparty.min.js') }}" data-order="pre-livewire"></script>
    <script src="{{ asset('vendor-wireui.js') }}" data-order="pre-livewire" @if(config('app.debug')) onload="console.log('[Diag] vendor-wireui.js loaded (pre-livewire app)')" @endif></script>
    <script>
        // Defensa: si algún script definió Alpine.persist como propiedad no configurable,
        // intentamos eliminarla antes de que Livewire vuelva a definir $persist.
        // (Evita 'Cannot redefine property: $persist').
        Object.defineProperty(window,'__lw_prepared',{value:true,writable:false});
        if(window.Alpine && Object.getOwnPropertyDescriptor(window.Alpine,'$persist')){
            try{ delete window.Alpine.$persist; }catch(e){ /* ignore */ }
        }
    </script>

    
    @livewireStyles
    @stack('css')
    @PwaHead
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- Navigation & Sidebar --}}
    @include('layouts.includes.app.navigation')
    @include('layouts.includes.app.sidebar')

    {{-- Main Content --}}
    <div class="p-4 sm:ml-64 ">
        {{-- Header: Breadcrumbs + Title + Action --}}
        <div class="mt-14 flex items-center">
            {{-- Breadcrumbs --}}
            
                @include('layouts.includes.app.breadcrumb')
            
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

    {{-- Livewire scripts (aislamiento: copia estática) --}}
    {{-- @livewireScripts --}}
    <script>
        window.Wireui = window.Wireui || { cache:{}, hooks:{}, dispatchHook(name,...p){(this.hooks[name]||[]).forEach(cb=>{try{cb(...p)}catch(e){console.error('[WireUI hook error]',name,e);}});}, hook(name,cb){(this.hooks[name]||(this.hooks[name]=[])).push(cb);} };
        // Config para Livewire (igual que en layout admin) antes de cargar script estático
        window.livewireScriptConfig = {
            uri: '{{ url('/livewire/update') }}',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            locale: '{{ app()->getLocale() }}',
            progressBar: true
        };
    </script>
    <script src="{{ asset('livewire-vendor.js') }}" data-origin="static"></script>
    <script>
        (function(){
            const start=()=>{ if(window.Livewire && !window.Livewire._started){ try{ window.Livewire.start(); if({{ config('app.debug') ? 'true' : 'false' }}) console.log('[Init] Livewire.start() (app)'); }catch(e){ if({{ config('app.debug') ? 'true' : 'false' }}) console.error('[Init] Error Livewire.start() (app)', e);} } };
            if(window.Livewire) start(); else { let c=0, iv=setInterval(()=>{ if(window.Livewire){ clearInterval(iv); start(); } else if(++c>40){ clearInterval(iv); if({{ config('app.debug') ? 'true' : 'false' }}) console.warn('[Init] Livewire no apareció (app)'); } },50); }
            document.addEventListener('livewire:init',()=>{ if({{ config('app.debug') ? 'true' : 'false' }}) console.log('[Diag] livewire:init (app)'); if(window.Wireui?.dispatchHook) window.Wireui.dispatchHook('loaded'); });
        })();
    </script>
    {{-- Registro de Service Worker (igual que admin) para permitir push si el usuario navega en layout app --}}
    <script>
        (function(){
            if('serviceWorker' in navigator){
                const version='sw-v6';
                const swUrl='/sw.js?v='+version;
                navigator.serviceWorker.getRegistration().then(reg=>{
                    const needs=!reg || !reg.active || !reg.active.scriptURL.includes(version);
                    if(needs){
                        navigator.serviceWorker.register(swUrl).then(r=>{ if({{ config('app.debug') ? 'true' : 'false' }}) console.log('[SW][app] registrado',r.scope,version); }).catch(e=>{ if({{ config('app.debug') ? 'true' : 'false' }}) console.error('[SW][app] error',e); });
                    } else { if({{ config('app.debug') ? 'true' : 'false' }}) console.log('[SW][app] ya activo',reg.scope,version); }
                });
            }
        })();
    </script>

    {{-- (Duplicated Livewire Tables assets removidos para evitar doble registro de plugins) --}}


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
        // Register Livewire event listener safely after Livewire initializes
        window.addEventListener('livewire:init', () => {
            Livewire.on('swal', (data) => {
                Swal.fire(data[0]);
            });
        });
    </script>

    @stack('js')

    {{-- Alerts --}}
    @include('layouts.includes.app.alerts')
    {{-- (Diagnostics eliminados) --}}
</body>
</html>
