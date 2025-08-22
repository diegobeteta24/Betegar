{{-- resources/views/admin/sales/payments/after_payment.blade.php --}}
<x-admin-layout title="Pago registrado | Betegar">
    <x-wire-card class="max-w-xl mx-auto text-center">
        <h2 class="text-lg font-bold mb-4">¡Pago registrado exitosamente!</h2>
        <p class="mb-6">Descarga el recibo de pago y serás redirigido al dashboard automáticamente.</p>
        <a id="download-link" href="{{ route('admin.sales.payments.pdf', $payment) }}" class="x-wire-button green" download>
            <i class="fa-solid fa-file-pdf"></i> Descargar recibo
        </a>
    </x-wire-card>
    <script>
        document.getElementById('download-link').addEventListener('click', function() {
            setTimeout(function() {
                window.location.href = '{{ route('admin.dashboard') }}';
            }, 1500); // Espera 1.5s para iniciar la descarga antes de redirigir
        });
    </script>
</x-admin-layout>
