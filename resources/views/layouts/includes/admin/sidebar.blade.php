{{-- resources/views/layouts/includes/admin/sidebar.blade.php --}}
<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-50 w-64 h-screen pt-20 transition-all duration-200 -translate-x-full will-change-[width]
        bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700 overflow-hidden"
       aria-label="Sidebar">
    {{-- Desktop brand (hidden on mobile) --}}
    <div class="hidden sm:flex items-center px-4 pb-4 absolute top-0 left-0 h-16 w-full border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <a href="/" class="flex items-center w-full">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 mr-3 select-none" onerror="if(!this.dataset.fallback){this.dataset.fallback=1;this.src='{{ asset('logo.png') }}';}else{this.replaceWith(Object.assign(document.createElement('span'),{className:'text-xl font-bold mr-3',textContent:'B'}));}" />
            <span class="brand-text text-xl font-semibold whitespace-nowrap dark:text-white">Betegar</span>
        </a>
    </div>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            @foreach($links as $link)
                @php
                    $html = $link->render();
                    // Add title tooltip in mini mode: inject title from visible text if not present
                    // Cheap heuristic: if an <a ...> contains <span>Label</span>, add title attr
                    try {
                        if($link instanceof \App\Services\Menu\MenuLink){
                            // add title="..." to first <a> if missing
                            if(!preg_match('/title\s*=/', $html)){
                                if(preg_match('/<span[^>]*>([^<]+)<\/span>/', $html, $m)){
                                    $label = trim($m[1]);
                                    $html = preg_replace('/<a\s+/','<a title="'.e($label).'" ', $html, 1);
                                }
                            }
                        }
                    } catch (\Throwable $e) { /* ignore */ }
                @endphp
                <li>{!! $html !!}</li>
            @endforeach
        </ul>
    </div>
</aside>
