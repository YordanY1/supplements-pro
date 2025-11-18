<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\DatabaseProductsService;

class Header extends Component
{
    public $categories = [];
    public $brands = [];

    public function mount(DatabaseProductsService $db)
    {
        $this->categories = $db->getCategories();
        $this->brands = $db->getBrands();
    }

    public function render()
    {
        return view('livewire.components.header', [
            'categories' => $this->categories,
            'brands' => $this->brands,
        ]);
    }
}
