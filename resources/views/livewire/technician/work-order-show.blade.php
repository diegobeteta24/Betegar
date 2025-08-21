<div class="bg-white border rounded-lg p-4">
    @if (session('saved'))
        <div class="p-2 mb-3 rounded bg-green-50 text-green-800">{{ session('saved') }}</div>
    @endif

    <h3 class="text-lg font-semibold mb-3">Nueva entrada</h3>
    @if($this->workOrder->status === 'done')
        <div class="p-2 mb-3 rounded bg-yellow-50 text-yellow-900">Esta orden está finalizada. No se permiten más entradas.</div>
    @endif
    <form wire:submit.prevent="saveEntry" class="space-y-3" enctype="multipart/form-data">
        <div>
            <label class="block text-sm font-medium">Progreso de hoy</label>
            <textarea wire:model="progress" class="mt-1 w-full border rounded p-2" rows="3" required></textarea>
            @error('progress') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Solicitudes al admin (opcional)</label>
            <textarea wire:model="requests" class="mt-1 w-full border rounded p-2" rows="2"></textarea>
            @error('requests') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Fotos (puedes seleccionar varias)</label>
            <input type="file" wire:model="images" multiple accept="image/*" class="mt-1" />
            @error('images.*') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <div x-data="signaturePadComponent()" class="space-y-2">
            <label class="block text-sm font-medium">Firma del cliente (dibujar)</label>
            <div class="border rounded p-2">
                <canvas x-ref="canvas" class="w-full h-40 bg-white rounded"></canvas>
            </div>
            <div class="flex gap-2">
                <button type="button" x-on:click="clearPad()" class="px-2 py-1 border rounded">Limpiar</button>
                <button type="button" x-on:click="emitData()" class="px-2 py-1 border rounded">Usar firma</button>
            </div>
            <input type="hidden" x-ref="output" />
            <input type="file" wire:model="signature" accept="image/*" class="hidden" />
            @error('signature') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Estado de la orden</label>
            <select wire:model="resultStatus" class="mt-1 border rounded p-2">
                <option value="pending">Pendiente</option>
                <option value="done">Finalizada</option>
            </select>
        </div>
        <div class="pt-2">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded" @disabled($this->workOrder->status === 'done')>Guardar entrada</button>
        </div>
    </form>

    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-3">Entradas</h3>
        @if(count($entries)===0)
            <p class="text-gray-500">Aún no hay entradas.</p>
        @else
            <ul class="space-y-4">
                @foreach($entries as $e)
                    <li class="border rounded p-3">
                        <div class="text-sm text-gray-500">{{ $e->work_date->format('d/m/Y') }} — {{ $e->user->name }}</div>
                        <div class="mt-2 whitespace-pre-line">{{ $e->progress }}</div>
                        @if($e->requests)
                            <div class="mt-2 text-sm text-gray-700"><span class="font-medium">Solicitudes:</span> {{ $e->requests }}</div>
                        @endif
                        @if($e->images && count($e->images))
                            <div class="mt-3 grid grid-cols-3 gap-2">
                                @foreach($e->images as $img)
                                    <a href="{{ asset('storage/'.$img->path) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$img->path) }}" class="w-full h-24 object-cover rounded" />
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        @if($e->signature)
                            <div class="mt-3">
                                <div class="text-sm text-gray-500">Firmado por {{ $e->signature_by }} ({{ optional($e->signed_at)->timezone(config('app.tz_guatemala','America/Guatemala'))->format('d/m H:i') }})</div>
                                <img src="{{ asset('storage/'.$e->signature->path) }}" class="mt-1 h-24 object-contain border rounded" />
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<script>
    function signaturePadComponent(){
        return {
            pad:null,
            init(){
                const canvas = this.$refs.canvas;
                // Resize canvas to element size
                const resize = ()=>{ canvas.width = canvas.clientWidth; canvas.height = canvas.clientHeight; if(this.pad){this.pad.clear();} };
                resize();
                window.addEventListener('resize', resize);
                this.pad = new window.SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)' });
            },
            clearPad(){ this.pad.clear(); },
            emitData(){
                if(this.pad.isEmpty()){ return; }
                const data = this.pad.toDataURL('image/png');
                // Push to Livewire property via Alpine -> Livewire entangle (fallback using $wire)
                this.$wire.signatureData = data;
            }
        }
    }
</script>

<!-- Include SignaturePad from CDN -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
