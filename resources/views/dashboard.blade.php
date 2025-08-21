<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(auth()->user()->hasRole('admin'))
                {{-- Admin ve dashboard técnico (todos los técnicos) y acceso a mapa --}}
                <livewire:technician.dashboard :adminMode="true" />
                <!-- Resumen agregado movido dentro del componente Livewire admin -->
                {{-- Si quieres dashboard contable/admin, descomenta la siguiente línea --}}
                {{-- @include('admin.dashboard') --}}
            @elseif(auth()->user()->hasRole('technician'))
                <livewire:technician.checkin-panel />
                <livewire:technician.dashboard :adminMode="false" />
            @else
                <div class="bg-white p-6 rounded shadow">No tienes acceso a ningún dashboard.</div>
            @endif
        </div>
    </div>
</x-app-layout>
