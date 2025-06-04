<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Livewire\Attributes\Url;
use Livewire\Component;

class Catalog extends Component
{
    #[Url(as: 'category', history: true)]
    public string $category = '';

    #[Url(as: 'brand', history: true)]
    public string $brand = '';

    #[Url]
    public string $sort = 'default';

    protected $listeners = ['filtersUpdated' => 'updateFilters'];

    public function updateFilters($data)
    {
        $this->category = $data['category'] ?? '';
        $this->brand = $data['brand'] ?? '';
        $this->sort = $data['sort'] ?? 'default';
    }

    public function render()
    {
        $query = Product::query()->with('brand', 'category');

        $categorySlugs = $this->category ? explode(',', $this->category) : [];
        $brandSlugs = $this->brand ? explode(',', $this->brand) : [];

        $categoryNames = [];
        $brandNames = [];

        if (!empty($categorySlugs)) {
            $categoryIds = Category::whereIn('slug', $categorySlugs)->pluck('id');
            $categoryNames = Category::whereIn('slug', $categorySlugs)->pluck('name')->toArray();
            $query->whereIn('category_id', $categoryIds);
        }

        if (!empty($brandSlugs)) {
            $brandIds = Brand::whereIn('slug', $brandSlugs)->pluck('id');
            $brandNames = Brand::whereIn('slug', $brandSlugs)->pluck('name')->toArray();
            $query->whereIn('brand_id', $brandIds);
        }


        $titleParts = [];

        if ($categoryNames) {
            $titleParts[] = implode(', ', $categoryNames);
        }

        if ($brandNames) {
            $titleParts[] = implode(', ', $brandNames);
        }

        $title = $titleParts
            ? 'Филтрирани продукти: ' . implode(' + ', $titleParts)
            : 'Всички продукти';

        if ($this->sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($this->sort === 'price_desc') {
            $query->orderByDesc('price');
        }

        return view('livewire.pages.catalog', [
            'products' => $query->get(),
            'title' => $title,
        ])->layout('layouts.app');
    }
}
