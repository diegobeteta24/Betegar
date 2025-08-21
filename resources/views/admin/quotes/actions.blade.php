<div class="flex items-center space-x-4">
    <x-wire-button green wire:click="openModal({{ $quote->id}})" title="Enviar por correo">
        <i class="fa-solid fa-envelope"></i>
    </x-wire-button>

    <x-wire-button blue href="{{ route('admin.quotes.pdf', $quote) }}" title="PDF">
        <i class="fa-solid fa-file-pdf"></i>
    </x-wire-button>

    <x-wire-button purple href="{{ route('admin.quotes.public', $quote) }}" target="_blank" title="Ver pública">
        <i class="fa-solid fa-link"></i>
    </x-wire-button>

    <x-wire-button amber href="{{ route('admin.quotes.edit', $quote) }}" title="Editar">
        <i class="fa-solid fa-pen"></i>
    </x-wire-button>
</div>
