<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\DatabaseProductsService;

class HomeWomenBrands extends Component
{
    public array $brands = [];

    public function mount(DatabaseProductsService $db)
    {
        $womenBrands = [
            'natures-way',
            'thorne',
            'vegavero',
            'lysi',
            'natural-factors',
            'dragon-herbs',
        ];

        $this->brands = collect($db->getBrands())
            ->filter(fn($b) => in_array($b['slug'], $womenBrands))
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.components.home-women-brands');
    }
}
