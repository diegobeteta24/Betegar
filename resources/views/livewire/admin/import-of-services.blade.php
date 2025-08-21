<div class="max-w-2xl mx-auto">
    <x-wire-card>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Importar Servicios</h2>
                <x-wire-button wire:click="downloadTemplate" gray class="text-sm">
                    <i class="fas fa-download"></i> Template
                </x-wire-button>
            </div>
            <div>
                <input type="file" wire:model="file" class="w-full border rounded p-2" />
                @error('file')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end">
                <x-wire-button wire:click="importServices" primary>Importar</x-wire-button>
            </div>
            @if(count($errors))
                <div class="mt-4">
                    <h3 class="font-semibold text-red-600 mb-2">Errores:</h3>
                    <ul class="list-disc ml-5 text-sm space-y-1">
                        @foreach($errors as $err)
                            <li>Fila {{ $err['row'] }}: {{ implode('; ', $err['errors']) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </x-wire-card>
</div>
