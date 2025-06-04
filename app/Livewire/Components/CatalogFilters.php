<?php

namespace App\Livewire\Components;

use App\Models\Category;
use App\Models\Brand;
use Livewire\Component;

class CatalogFilters extends Component
{
    public array $categorySlugs = [];
    public array $brandSlugs = [];
    public string $sort = 'default';

    public function mount()
    {
        $this->categorySlugs = request()->query('category')
            ? explode(',', request()->query('category'))
            : [];

        $this->brandSlugs = request()->query('brand')
            ? explode(',', request()->query('brand'))
            : [];

        $this->sort = request()->query('sort') ?? 'default';
    }


    public function toggleCategory($slug)
    {
        if (in_array($slug, $this->categorySlugs)) {
            $this->categorySlugs = array_diff($this->categorySlugs, [$slug]);
        } else {
            $this->categorySlugs[] = $slug;
        }

        $this->dispatch('filtersUpdated', $this->currentFilters());
    }

    public function toggleBrand($slug)
    {
        if (in_array($slug, $this->brandSlugs)) {
            $this->brandSlugs = array_diff($this->brandSlugs, [$slug]);
        } else {
            $this->brandSlugs[] = $slug;
        }

        $this->dispatch('filtersUpdated', $this->currentFilters());
    }

    public function updated()
    {
        $this->dispatch('filtersUpdated', $this->currentFilters());
    }

    protected function currentFilters(): array
    {
        return [
            'category' => implode(',', $this->categorySlugs),
            'brand' => implode(',', $this->brandSlugs),
            'sort' => $this->sort,
        ];
    }

    public function render()
    {
        return view('livewire.components.catalog-filters', [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }
}
