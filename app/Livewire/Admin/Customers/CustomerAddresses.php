<?php

namespace App\Livewire\Admin\Customers;

use Livewire\Component;
use App\Models\Customer;
use App\Models\CustomerAddress;

class CustomerAddresses extends Component
{
    public Customer $customer;
    public $label = '';
    public $address = '';

    protected function rules(): array
    {
        return [
            'label' => ['nullable','string','max:50'],
            'address' => ['required','string','max:255'],
        ];
    }

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function addAddress()
    {
        $data = $this->validate();
        $this->customer->addresses()->create($data + ['is_primary' => !$this->customer->addresses()->exists()]);
        $this->reset(['label','address']);
        $this->dispatch('sweet-alert', [
            'icon' => 'success',
            'title' => 'Dirección agregada',
            'timer' => 1800,
            'showConfirmButton' => false,
        ]);
    }

    public function setPrimary($id)
    {
        $address = $this->customer->addresses()->whereKey($id)->firstOrFail();
        $this->customer->addresses()->update(['is_primary' => false]);
        $address->update(['is_primary' => true]);
        $this->dispatch('sweet-alert', [
            'icon' => 'success',
            'title' => 'Dirección principal actualizada',
            'timer' => 1500,
            'showConfirmButton' => false,
        ]);
    }

    public function deleteAddress($id)
    {
        $address = $this->customer->addresses()->whereKey($id)->firstOrFail();
        $wasPrimary = $address->is_primary;
        $address->delete();
        if($wasPrimary) {
            $next = $this->customer->addresses()->first();
            if($next) { $next->update(['is_primary' => true]); }
        }
        $this->dispatch('sweet-alert', [
            'icon' => 'success',
            'title' => 'Dirección eliminada',
            'timer' => 1500,
            'showConfirmButton' => false,
        ]);
    }

    public function render()
    {
        $addresses = $this->customer->addresses()->orderByDesc('is_primary')->orderBy('id')->get();
        return view('livewire.admin.customers.customer-addresses', compact('addresses'));
    }
}
