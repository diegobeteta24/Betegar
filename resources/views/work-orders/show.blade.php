<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border rounded-lg p-4">
                <h2 class="text-xl font-semibold">Orden #{{ $workOrder->id }}</h2>
                <p class="text-gray-600">Cliente: {{ $workOrder->customer->name ?? 'N/A' }}</p>
                <p class="text-gray-600">Dirección: {{ $workOrder->address }}</p>
                <p class="text-gray-600">Objetivo: {{ $workOrder->objective }}</p>
            </div>

            <livewire:technician.work-order-show :workOrder="$workOrder" />
        </div>
    </div>
</x-app-layout>
