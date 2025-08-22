<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar gasto
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-sm text-gray-600">Sube el comprobante obligatorio para registrar el gasto.</p>

                    <form id="expense-form" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Monto</label>
                            <input name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full border rounded p-2" placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea name="description" class="mt-1 block w-full border rounded p-2" rows="3" placeholder="Detalle del gasto" minlength="3" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Comprobante (solo imagen: jpg, jpeg, png) — Obligatorio</label>
                            <input id="voucher" name="voucher" type="file" accept="image/*" class="mt-1 block w-full border rounded p-2 bg-white" required>
                            <div id="preview" class="mt-2"></div>
                            <p class="text-xs text-gray-500 mt-1">Tamaño máximo: 8 MB.</p>
                        </div>
                        <div class="flex gap-2">
                            <button id="submitBtn" type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Guardar</button>
                            <a href="/" class="px-4 py-2 bg-gray-100 rounded">Cancelar</a>
                        </div>
                    </form>
                    <script>
                        (function(){
                            const form = document.getElementById('expense-form');
                            const voucher = document.getElementById('voucher');
                            const preview = document.getElementById('preview');
                            const MAX = 8 * 1024 * 1024; // 8MB

                            voucher.addEventListener('change', () => {
                                preview.innerHTML = '';
                                const f = voucher.files[0];
                                if (!f) return;
                                if (f.size > MAX) {
                                    alert('El archivo excede 8 MB.');
                                    voucher.value = '';
                                    return;
                                }
                                if (f.type.startsWith('image/')){
                                    const img = document.createElement('img');
                                    img.className = 'mt-1 max-h-48 rounded border';
                                    img.alt = 'Vista previa';
                                    img.src = URL.createObjectURL(f);
                                    preview.appendChild(img);
                                } else {
                                    const span = document.createElement('span');
                                    span.textContent = 'Archivo PDF adjuntado: ' + f.name;
                                    span.className = 'text-sm text-gray-600';
                                    preview.appendChild(span);
                                }
                            });

                            form.addEventListener('submit', async (e) => {
                                e.preventDefault();
                                const f = voucher.files[0];
                                if (!f) { alert('Debes adjuntar un comprobante.'); return; }
                                // Construir el FormData para API
                                const fd = new FormData();
                                fd.append('description', form.description.value.trim());
                                fd.append('amount', form.amount.value);
                                fd.append('voucher', f);
                                try {
                                    const res = await fetch('/api/expenses', {
                                        method: 'POST',
                                        credentials: 'same-origin',
                                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                                        body: fd
                                    });
                                    if (!res.ok) {
                                        const t = await res.text();
                                        throw new Error('Error ' + res.status + ': ' + t);
                                    }
                                    await res.json();
                                    // Redirigir directamente al dashboard del técnico (sin alert bloqueante)
                                    window.location.replace('{{ route('dashboard') }}');
                                } catch(err){
                                    console.error('Error registrando gasto', err);
                                    alert('No se pudo registrar el gasto: ' + err.message);
                                }
                            });
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
