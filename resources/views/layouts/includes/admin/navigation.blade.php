<nav class="fixed top-0 left-0 z-[60] w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3 sm:pl-64 transition-all">
    <div class="flex items-center justify-between">
      <div class="flex items-center justify-start rtl:justify-end">
        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
               <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
         </button>
    <a href="/" class="flex ms-2 md:me-24 items-center">
        <img id="appLogo" src="{{ asset('images/logo.png') }}" class="h-8 me-3 select-none"
            alt="Logo"
            onerror="if(!this.dataset.fallback){this.dataset.fallback=1;this.src='{{ asset('logo.png') }}';}else{this.replaceWith(Object.assign(document.createElement('span'),{className:'text-xl font-bold me-3',textContent:'B'}));}" />
    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Betegar</span>
        </a>
        <button type="button" id="collapseDesktopSidebarBtn" title="Contraer sidebar" class="hidden sm:inline-flex ml-2 p-2 rounded text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 focus:outline-none" aria-pressed="false">
            <svg class="w-5 h-5 rotate-0 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        </button>
      </div>
            <div class="flex items-center gap-3">
                <button id="themeToggleBtn" type="button" class="p-2 rounded border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" title="Cambiar tema">
                        <svg id="themeIconSun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                        <svg id="themeIconMoon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>
          <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>
                            @if(auth()->user()->hasRole('admin'))
                                <x-dropdown-link href="{{ url('/') }}">
                                    Dashboard Técnico
                                </x-dropdown-link>
                                <div class="border-t border-gray-200"></div>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
        </div>
    </div>
    </div>
</nav>
<script>
document.addEventListener('DOMContentLoaded',()=>{
    const btn = document.getElementById('collapseDesktopSidebarBtn');
    const root = document.documentElement;
    const LS_KEY = 'sidebar:collapsed:v1';
    function apply(state){
        if(state){
            root.classList.add('sidebar-collapsed');
            btn.setAttribute('aria-pressed','true');
            btn.title = 'Expandir sidebar';
            btn.querySelector('svg').style.transform='rotate(180deg)';
        } else {
            root.classList.remove('sidebar-collapsed');
            btn.setAttribute('aria-pressed','false');
            btn.title = 'Contraer sidebar';
            btn.querySelector('svg').style.transform='rotate(0deg)';
        }
    }
    let collapsed = localStorage.getItem(LS_KEY)==='1';
    apply(collapsed);
    btn.addEventListener('click',()=>{
        collapsed = !collapsed; localStorage.setItem(LS_KEY, collapsed?'1':'0'); apply(collapsed);
        setTimeout(()=>window.dispatchEvent(new Event('resize')),210);
    });
});
</script>
<script>
// Dark/Light toggle
document.addEventListener('DOMContentLoaded',()=>{
    const btn=document.getElementById('themeToggleBtn');
    if(!btn) return;
    const iconSun=document.getElementById('themeIconSun');
    const iconMoon=document.getElementById('themeIconMoon');
    const LS_KEY='theme:dark';
    function apply(){
        const dark=localStorage.getItem(LS_KEY)==='1';
        document.documentElement.classList.toggle('dark',dark);
        iconSun.classList.toggle('hidden',!dark);
        iconMoon.classList.toggle('hidden',dark);
    }
    apply();
    btn.addEventListener('click',()=>{ const cur=localStorage.getItem(LS_KEY)==='1'; localStorage.setItem(LS_KEY,cur?'0':'1'); apply(); });
});
</script>