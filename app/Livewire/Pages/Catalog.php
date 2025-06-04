<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Livewire\Component;

class Catalog extends Component
{
    public ?Category $category = null;
    public ?Brand $brand = null;

    public function mount($category = null, $brand = null)
    {
        if ($category instanceof Category) {
            $this->category = $category;
        }

        if ($brand instanceof Brand) {
            $this->brand = $brand;
        }
    }

    public function render()
    {
        $query = Product::query()->with('brand', 'category');

        if ($this->category) {
            $query->where('category_id', $this->category->id);
        }

        if ($this->brand) {
            $query->where('brand_id', $this->brand->id);
        }

        return view('livewire.pages.catalog', [
            'products' => $query->get(),
            'title' => $this->category?->name ?? $this->brand?->name ?? 'Всички продукти',
        ])->layout('layouts.app');
    }
}
