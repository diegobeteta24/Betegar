<?php

namespace App\Livewire\Admin\Services;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class CreateService extends Component
{
    public $name = '';
    public $description = '';
    public $price = '';
    public $category_id = '';

    public $categories = [];

    protected function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'price' => ['required','numeric','min:0'],
            'category_id' => ['required','exists:categories,id'],
        ];
    }

    public function mount()
    {
        $this->categories = Category::query()->orderBy('name')->get();
        if($this->categories->count()) {
            $this->category_id = (string)$this->categories->first()->id;
        }
    }

    public function save()
    {
        $data = $this->validate();
        $data['type'] = 'service';
        Product::create($data);
        $this->reset(['name','description','price']);
        $this->dispatch('serviceCreated');
        $this->dispatch('sweet-alert', [
            'icon' => 'success',
            'title' => 'Servicio creado',
            'timer' => 2000,
            'showConfirmButton' => false,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.services.create-service');
    }
}
