<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\DatabaseProductsService;

class HomeFeaturedBrands extends Component
{
    public array $brands = [];

    public function mount(DatabaseProductsService $db)
    {
        $brands = $db->getBrands();

        $this->brands = collect($brands)
            ->take(6)
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.components.home-featured-brands');
    }
}
