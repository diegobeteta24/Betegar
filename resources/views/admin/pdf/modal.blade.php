<x-wire-modal-card wire:model="form.open" width="lg">
<p class="text-xl text-center mb-2">
Enviar email
</p>
<p class="text-lg text-center uppercase mb-2">
    {{$form['document']}}
</p>

<p class="text-center uppercase mb-2">
    {{$form['client']}}
</p>

<form wire:submit="sendEmail">
    <x-wire-input
        label="Correo Electronico"
        wire:model="form.email"
        class="mb-4"
    />


<x-wire-button class="w-full" type="submit">
    Enviar correo
</x-wire-button>
</form>

</x-wire-modal-card>