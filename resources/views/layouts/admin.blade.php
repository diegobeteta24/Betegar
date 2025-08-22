{{-- resources/views/layouts/admin.blade.php --}}
@php
    // Obtener atributos pasados al componente (class based) si no se definieron como props
    $attrBreadcrumbs = isset($breadcrumbs)
        ? $breadcrumbs
        : (isset($attributes) && method_exists($attributes,'get') ? ($attributes->get('breadcrumbs') ?? []) : []);
    // Normalizar a array sencillo
    if(!is_array($attrBreadcrumbs)) { $attrBreadcrumbs = []; }
    $breadcrumbs = $attrBreadcrumbs;
    // Título explícito > último breadcrumb > nombre app
    $explicitTitle = isset($title)
        ? $title
        : (isset($attributes) && method_exists($attributes,'get') ? $attributes->get('title') : null);
    if($explicitTitle){
        $pageTitle = $explicitTitle;
    } elseif(!empty($breadcrumbs)) {
        $last = $breadcrumbs[array_key_last($breadcrumbs)] ?? null;
        $pageTitle = is_array($last) && isset($last['name']) ? $last['name'] : config('app.name', 'Laravel');
    } else {
        $pageTitle = config('app.name', 'Laravel');
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

    {{-- Vite: TailwindCSS (con Flowbite) + JS (sin arrancar Alpine) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('css')
    @PwaHead
</head>
<body class="font-sans antialiased bg-gray-50">
        <style>
            /* Improved PC sidebar mini-mode: smooth, hover-expand overlay, no layout jump */
            @media (min-width: 640px){
                /* Base sidebar width with animation */
                #logo-sidebar{ width:16rem; transition: width .25s ease; will-change: width; }
                /* Mini state */
                .sidebar-collapsed #logo-sidebar{
                    width:4.5rem; /* a bit wider for comfortable hit targets */
                    overflow:visible; /* allow hover overlay */
                }
                /* Hide labels in mini state */
                .sidebar-collapsed #logo-sidebar ul li span:not(.keep){ display:none }
                .sidebar-collapsed #logo-sidebar .brand-text{ display:none }
                .sidebar-collapsed #logo-sidebar ul li a{ justify-content:center }
                .sidebar-collapsed #logo-sidebar ul li a i{ margin-right:0 }

                /* Keep content aligned to mini width (no jump) */
                .sidebar-collapsed .sm\:ml-64{ margin-left:4.5rem !important }
                .sidebar-collapsed nav .sm\:pl-64{ padding-left:4.5rem !important }

                /* Hover-expand the sidebar over content without moving layout */
                .sidebar-collapsed #logo-sidebar:hover{
                    width:16rem; box-shadow: 0 6px 18px rgba(0,0,0,.12);
                }
                .sidebar-collapsed #logo-sidebar:hover ul li span{ display:inline !important; white-space:nowrap }
                .sidebar-collapsed #logo-sidebar:hover .brand-text{ display:inline !important }
                .sidebar-collapsed #logo-sidebar:hover ul li a{ justify-content:flex-start }
            }
        </style>

    {{-- Navigation & Sidebar --}}
    @include('layouts.includes.admin.navigation')
    @include('layouts.includes.admin.sidebar')

    {{-- Main Content --}}
    <div class="p-4 sm:ml-64 ">
        {{-- Header: Breadcrumbs + Title + Action --}}
        <script>
            // Deep WireUI select debug
            (function(){
                function dumpSelectStates(tag){
                    const nodes = Array.from(document.querySelectorAll('[x-data]')).filter(el=>/wireui_select/.test(el.getAttribute('x-data')||''));
                    console.log(`%c[DBG][selects:${tag}] count=${nodes.length}`,'color:#b0f');
                    nodes.forEach((el,i)=>{
                        const st = el.__x ? el.__x.$data || el.__x.data : {};
                        // try common properties
                        const snapshot = {};
                        ['options','items','data','filteredOptions','search','loading','selected','value'].forEach(k=>{ if(st && k in st) snapshot[k]=st[k]; });
                        console.log('%c[DBG][select]','color:#b0f', i, snapshot, el);
                    });
                }
                document.addEventListener('alpine:init', ()=>{
                    console.log('[DBG] alpine:init event (debug script)');
                    setTimeout(()=>dumpSelectStates('post-alpine:init'),200);
                });
                window.addEventListener('load',()=>setTimeout(()=>dumpSelectStates('onload'),500));
                // Observe DOM mutations to detect addition of wireui_select components
                const mo = new MutationObserver(muts=>{
                    let added=false;
                    muts.forEach(m=>m.addedNodes&&m.addedNodes.forEach(n=>{ if(n.nodeType===1 && n.querySelector && n.querySelector('[x-data*="wireui_select" ]')) added=true; }));
                    if(added) dumpSelectStates('mutation');
                });
                mo.observe(document.documentElement,{subtree:true,childList:true});
                // Expose manual command
                window.__dumpWireuiSelects = ()=>dumpSelectStates('manual');
            })();
        </script>
    <div class="mt-14 flex items-center">
            {{-- Breadcrumbs --}}
            
                @include('layouts.includes.admin.breadcrumb')
            
            @if(isset($action))
                <div class="ml-auto">{{ $action }}</div>
            @elseif(View::hasSection('action'))
                <div class="ml-auto">@yield('action')</div>
            @endif
        </div>

        {{-- Contenido de la página: si viene como componente ($slot) o como sección yield('content') --}}
        @if(isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </div>

    {{-- Modals --}}
    @stack('modals')

    {{-- CARGA DE LIBRERÍAS QUE ESCUCHAN 'alpine:init' ANTES DE INICIAR LIVEWIRE/ALPINE --}}
    {{-- Livewire Tables & WireUI (registran Alpine.data(...) en alpine:init) --}}
    <link rel="stylesheet" href="{{ asset('vendor/rappasoft/livewire-tables/css/laravel-livewire-tables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/rappasoft/livewire-tables/css/laravel-livewire-tables-thirdparty.min.css') }}" />
    <script src="{{ asset('vendor/rappasoft/livewire-tables/js/laravel-livewire-tables.min.js') }}" data-order="pre-livewire"></script>
    <script src="{{ asset('vendor/rappasoft/livewire-tables/js/laravel-livewire-tables-thirdparty.min.js') }}" data-order="pre-livewire"></script>
    <script src="{{ asset('vendor-wireui.js') }}" data-order="pre-livewire" onload="console.log('[Diag] vendor-wireui.js loaded (pre-livewire)')"></script>
    <script>
        // Defensa contra redefinición de $persist al iniciar Livewire
        if(window.Alpine && Object.getOwnPropertyDescriptor(window.Alpine,'$persist')){
            try{ delete window.Alpine.$persist; }catch(e){ /* noop */ }
        }
    </script>

    {{-- Livewire script estático (la versión dinámica se trunca) --}}
    <script>
        window.livewireScriptConfig = {
            uri: '{{ url('/livewire/update') }}',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            locale: '{{ app()->getLocale() }}',
            progressBar: true
        };
        // Only create minimal Wireui object if the CDN script hasn't defined it yet
        if(!window.Wireui){
            window.Wireui = { cache:{}, hooks:{}, dispatchHook(name,...p){(this.hooks[name]||[]).forEach(cb=>{try{cb(...p)}catch(e){console.error('[WireUI hook error]',name,e);}});}, hook(name,cb){(this.hooks[name]||(this.hooks[name]=[])).push(cb);} };
            console.log('[Diag] Fallback Wireui stub created BEFORE CDN load');
        }
    </script>
    {{-- WireUI hooks (antes de Livewire). WireUI JS cargado por CDN fallback --}}
    <script>{!! WireUi::directives()->hooksScript() !!}
    window.Wireui.hook('loaded',()=>console.log('[WireUI] loaded'));
    document.addEventListener('DOMContentLoaded',()=>console.log('[DOM] ready'));
    </script>
    {{-- Livewire después para disparar livewire:init --}}
        <script src="{{ asset('livewire-vendor.js') }}" data-origin="static" data-update-uri="{{ url('/livewire/update') }}" data-csrf="{{ csrf_token() }}"></script>
        <script>
            // Secuencia controlada: scripts de tablas + wireui ya añadieron listeners 'alpine:init'.
            // Ahora arrancamos Livewire (que internamente iniciará Alpine y disparará 'alpine:init').
            (function(){
                const start = ()=>{
                    if(window.Livewire && !window.Livewire._started){
                        try { window.Livewire.start(); console.log('[Init] Livewire.start() (admin)'); }
                        catch(e){ console.error('[Init] Error Livewire.start()', e); }
                    }
                };
                if(window.Livewire) start(); else {
                    let c=0, iv=setInterval(()=>{ if(window.Livewire){ clearInterval(iv); start(); } else if(++c>40){ clearInterval(iv); console.warn('[Init] Livewire no apareció'); } },50);
                }
                document.addEventListener('livewire:init', ()=>{
                    console.log('[Diag] livewire:init (admin)');
                    if(window.Wireui?.dispatchHook) window.Wireui.dispatchHook('loaded');
                });
            })();
        </script>
        <script>
            // Interceptor fetch: añade CSRF a POST y convierte POST de selects públicos a GET para evitar 419
            (function(){
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const origFetch = window.fetch;
                window.fetch = function(input, init={}){
                    let url = (typeof input === 'string')? input : input.url;
                    const sameOrigin = url.startsWith('/') || url.startsWith(location.origin);
                    const method = (init.method || 'GET').toUpperCase();
                    if(sameOrigin && method==='POST'){
                        init.headers = new Headers(init.headers || {});
                        if(!init.headers.has('X-CSRF-TOKEN')) init.headers.set('X-CSRF-TOKEN', csrf);
                    }
                    return origFetch(url, init);
                };
            })();
        </script>
    {{-- Registro de Service Worker (habilitado para Push). Forzamos actualización si cambia este hash. --}}
    <script>
        (function(){
            if('serviceWorker' in navigator){
                const version = 'sw-v6'; // incrementa cuando modifiques sw.js
                const swUrl = '/sw.js?v='+version;
                navigator.serviceWorker.getRegistration().then(reg=>{
                    const needsRegister = !reg || !reg.active || !reg.active.scriptURL.includes(version);
                    if(needsRegister){
                        navigator.serviceWorker.register(swUrl).then(r=>{
                            console.log('[SW] registrado', r.scope, version);
                        }).catch(e=>console.error('[SW] error registro', e));
                    } else {
                        console.log('[SW] ya activo', reg.scope, version);
                    }
                });
            } else {
                console.warn('[SW] no soportado en este navegador');
            }
        })();
    </script>

    {{-- (Las tablas y wireui ya cargaron antes de Livewire) --}}

    {{-- Vite JS bundle already contains Flowbite behavior --}}


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
            Livewire.on('public-quote-ready', (data) => {
                let url = (data && data.url) || (Array.isArray(data) && data[0]?.url);
                if(!url && Array.isArray(data)){
                    // If first element is string
                    url = typeof data[0] === 'string' ? data[0] : url;
                }
                if(url){
                    // Try using an already opened temp window (if any stored globally)
                    if(window.__publicQuoteWin && !window.__publicQuoteWin.closed){
                        window.__publicQuoteWin.location = url;
                        window.__publicQuoteWin.focus();
                    } else {
                        window.open(url, '_blank','noopener');
                    }
                }
            });
        });
    </script>

    @stack('js')

    {{-- Alerts --}}
    @include('layouts.includes.admin.alerts')
    {{-- (Diagnostics eliminados) --}}
</body>
</html>
