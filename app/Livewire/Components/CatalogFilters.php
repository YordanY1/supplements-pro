<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\SupplementsAggregatorService;

class CatalogFilters extends Component
{
    public array $categorySlugs = [];
    public array $brandSlugs = [];
    public string $sort = 'default';
    public int $perPage = 12;

    public array $categories = [];
    public array $brands = [];

    public function mount(SupplementsAggregatorService $supplements)
    {
        $this->categories = $supplements->getCategories();
        $this->brands = $supplements->getBrands();

        $this->categorySlugs = request()->query('category')
            ? explode(',', request()->query('category'))
            : [];

        $this->brandSlugs = request()->query('brand')
            ? explode(',', request()->query('brand'))
            : [];

        $this->sort = request()->query('sort') ?? 'default';
        $this->perPage = (int)(request()->query('perPage') ?? 12);
    }

    public function toggleCategory($slug)
    {
        $this->categorySlugs = in_array($slug, $this->categorySlugs)
            ? array_diff($this->categorySlugs, [$slug])
            : [...$this->categorySlugs, $slug];

        $this->dispatch('filtersUpdated', $this->currentFilters());
    }

    public function toggleBrand($slug)
    {
        $this->brandSlugs = in_array($slug, $this->brandSlugs)
            ? array_diff($this->brandSlugs, [$slug])
            : [...$this->brandSlugs, $slug];

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
            'perPage' => $this->perPage,
            'sort' => $this->sort,
        ];
    }

    public function render()
    {
        return view('livewire.components.catalog-filters');
    }
}
