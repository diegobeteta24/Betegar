{{-- resources/views/livewire/admin/bank-account-create.blade.php --}}
<div>
    <x-wire-card>
        <form wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-wire-input
                    label="Account Name"
                    wire:model="name"
                    placeholder="Enter account name" />

                <x-wire-input
                    label="Initial Balance (Q)"
                    wire:model="initial_balance"
                    type="number"
                    step="0.01"
                    placeholder="0.00" />

                <x-wire-native-select
                    label="Currency"
                    wire:model="currency"
                    placeholder="Select currency">
                    <option value="GTQ">GTQ</option>
                    {{-- agregar más monedas si lo deseas --}}
                </x-wire-native-select>
            </div>

            <div>
                <x-wire-textarea
                    label="Description"
                    wire:model="description"
                    placeholder="Optional description" />
            </div>

            <div class="flex justify-end">
                <x-wire-button
                    type="submit"
                    icon="check"
                    spinner="save">
                    Create Account
                </x-wire-button>
            </div>

        </form>
    </x-wire-card>
</div>
