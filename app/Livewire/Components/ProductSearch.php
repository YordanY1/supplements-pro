<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Product;

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

        $this->results = Product::query()
            ->with(['brand', 'category'])
            ->where('name', 'like', '%' . $this->query . '%')
            ->orWhereHas('brand', function ($q) {
                $q->where('name', 'like', '%' . $this->query . '%');
            })
            ->orWhereHas('category', function ($q) {
                $q->where('name', 'like', '%' . $this->query . '%');
            })
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'brand_name' => $product->brand->name ?? '',
                    'category' => $product->category->name ?? '',
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.components.product-search');
    }
}
