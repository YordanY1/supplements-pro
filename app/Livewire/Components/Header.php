<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\DatabaseProductsService;
use App\Models\Product;

class Header extends Component
{
    public $categories = [];
    public $brands = [];

    public string $search = '';
    public array $results = [];
    public bool $searchOpen = false;

    public function mount(DatabaseProductsService $db)
    {
        $this->categories = $db->getCategories();
        $this->brands = $db->getBrands();
    }

    public function updatedSearch()
    {
        $query = trim($this->search);

        if (strlen($query) < 2) {
            $this->results = [];
            $this->searchOpen = false;
            return;
        }

        $this->results = Product::query()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('brand_name', 'like', "%{$query}%")
                    ->orWhere('category', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['title', 'slug', 'brand_name', 'image', 'price'])
            ->toArray();

        $this->searchOpen = true;
    }

    public function goTo($slug)
    {
        return redirect()->route('product.show', $slug);
    }

    public function closeSearch()
    {
        $this->searchOpen = false;
    }

    public function render()
    {
        return view('livewire.components.header');
    }
}
