<div>
    <x-wire-card>
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Importar Cuentas Bancarias desde Excel</h1>
        <p class="text-sm text-gray-500 mb-4">Columnas: bank_name, account_name, account_number, currency (opcional), initial_balance (opcional)</p>
        <x-wire-button blue wire:click="downloadTemplate" class="mb-4">
            <i class="fas fa-file-excel"></i>
            Descargar Plantilla
        </x-wire-button>
        <input type="file" wire:model="file" accept=".xlsx, .xls, .csv" />
        <x-input-error for="file" class="mt-2" />
        <div class="mt-4">
            <x-wire-button green wire:click="importBankAccounts" wire:loading.attr="disabled" wire:target="file" spinner="importBankAccounts">
                <i class="fas fa-upload mr-2"></i>
                Importar Cuentas
            </x-wire-button>
        </div>
        @if(count($errors))
            <div class="mt-4">
                <div class="p-4 bg-yellow-100 border border-yellow-300 rounded-md text-yellow-800 mb-3">
                    @if ($importedCount)
                        <i class="fas fa-triangle-exclamation mr-2"></i>
                        <strong>Importación parcial</strong>
                        <p class="mt-1 text-sm">Algunas cuentas fallaron.</p>
                    @else
                        <i class="fas fa-xmark-circle mr-2"></i>
                        <strong>Sin importaciones</strong>
                        <p class="mt-1 text-sm">Todas las filas tuvieron errores.</p>
                    @endif
                </div>
                <ul class="space-y-2">
                    @foreach ($errors as $error)
                        <li class="p-3 bg-red-50 border border-red-300 rounded-md">
                            <p class="text-red-700 font-semibold">
                                <i class="fas fa-file-pen"></i>
                                Fila {{ $error['row'] }}:
                            </p>
                            <ul class="list-disc list-inside mt-1">
                                @foreach ($error['errors'] as $message)
                                    <li class="text-red-600 text-sm">{{ $message }}</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-wire-card>
</div>
