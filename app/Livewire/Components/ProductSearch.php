<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class ProductSearch extends Component
{
    public string $query = '';
    public array $results = [];

    public function updatedQuery()
    {
        $this->results = [];

        if (strlen($this->query) < 2) {
            return;
        }

        $products = Cache::get('supplements.all_products', []);

        $this->results = collect($products)
            ->filter(function ($product) {
                return stripos($product['title'], $this->query) !== false
                    || stripos($product['brand_name'] ?? '', $this->query) !== false
                    || stripos($product['category'] ?? '', $this->query) !== false;
            })
            ->take(10)
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.components.product-search');
    }
}
