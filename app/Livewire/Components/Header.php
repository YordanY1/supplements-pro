<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\SupplementsAggregatorService;

class Header extends Component
{
    public $categories = [];
    public $brands = [];

    public function mount(SupplementsAggregatorService $supplements)
    {
        $this->categories = $supplements->getCategories();
        $this->brands = $supplements->getBrands();
    }

    public function render()
    {
        return view('livewire.components.header', [
            'categories' => $this->categories,
            'brands' => $this->brands,
        ]);
    }
}
