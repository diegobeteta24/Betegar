<div>
    <x-wire-card>
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Importar Cotizaciones desde Excel</h1>
        <x-wire-button blue wire:click="downloadTemplate">
            <i class="fas fa-file-excel"></i>
            Descargar Plantilla
        </x-wire-button>
        <p class="text-sm text-gray-500 mt-1">Completa la plantilla con encabezados de cotizaciones (sin líneas de productos) y súbela aquí.</p>
        <div class="mt-4">
            <input type="file" wire:model="file" accept=".xlsx, .xls, .csv" />
            <x-input-error for="file" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-wire-button green wire:click="importQuotes" wire:loading.attr="disabled" wire:target="file" spinner="importQuotes">
                <i class="fas fa-upload mr-2"></i>
                Importar Cotizaciones
            </x-wire-button>
        </div>
        @if($importedCount)
            <div class="mt-4 p-3 bg-green-50 border border-green-300 rounded-md text-green-700 text-sm">
                <i class="fas fa-circle-check mr-1"></i> Se importaron {{ $importedCount }} cotizaciones.
            </div>
        @endif
        @if($errors && count($errors))
            <div class="mt-6">
                <div class="p-4 bg-yellow-100 border border-yellow-300 rounded-md text-yellow-800 mb-3">
                    <strong>Errores detectados</strong>
                    <p class="mt-1 text-sm">Algunas filas no se importaron:</p>
                </div>
                <ul class="space-y-2">
                    @foreach($errors as $error)
                        <li class="p-3 bg-red-50 border border-red-300 rounded-md text-sm text-red-700">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-wire-card>
</div>
