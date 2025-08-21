<div class="p-4">
    <div class="mb-4">
        <h2 class="text-lg font-semibold">Recordatorios / CRM (MVP)</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="sm:col-span-2">
            <label class="block">Asociar a cotización</label>
            <select wire:model="quote_id" class="w-full rounded border p-2">
                <option value="">-- Ninguna --</option>
                @foreach($quotes as $q)
                    <option value="{{ $q->id }}">#{{ $q->serie }}-{{ str_pad($q->correlative,4,'0',STR_PAD_LEFT) }} - {{ $q->customer?->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Tipo</label>
            <select wire:model="type" class="w-full rounded border p-2">
                <option value="follow_up">Seguimiento</option>
                <option value="call">Llamada</option>
                <option value="email">Email</option>
                <option value="task">Tarea</option>
            </select>
        </div>
    </div>

    <div class="mb-4">
        <label>Notas</label>
        <textarea wire:model="notes" class="w-full border rounded p-2" rows="3"></textarea>
    </div>
    <div class="mb-4">
        <label>Recordar en</label>
        <input type="datetime-local" wire:model="remind_at" class="w-full border rounded p-2" />
    </div>
    <div class="mb-6">
        <button wire:click.prevent="store" class="bg-blue-600 text-white px-4 py-2 rounded">Crear recordatorio</button>
    </div>

    <div>
        <h3 class="text-md font-medium mb-2">Próximos recordatorios</h3>
        <div class="space-y-2">
            @foreach($reminders as $rem)
                <div class="p-3 bg-white shadow rounded flex items-start justify-between">
                    <div>
                        <div class="text-sm text-gray-600">{{ $rem->remind_at?->format('d/m/Y H:i') ?? 'Sin fecha' }} • {{ $rem->type }}</div>
                        <div class="font-medium">
                            @if($rem->quote)
                                #{{ $rem->quote->serie }}-{{ str_pad($rem->quote->correlative,4,'0',STR_PAD_LEFT) }}
                            @else
                                <span class="text-gray-400">Sin cotización</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-700">{{ $rem->notes }}</div>
                        <div class="text-xs text-gray-500">Creado por: {{ $rem->user?->name ?? 'Sistema' }}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button wire:click="toggleCompleted({{ $rem->id }})" class="px-3 py-1 rounded border text-sm">{{ $rem->completed ? 'Hecho' : 'Marcar' }}</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
