<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Services\SupplementsAggregatorService;
use Illuminate\Support\Str;

class Catalog extends Component
{
    #[Url(as: 'category', history: true)]
    public string $category = '';

    #[Url(as: 'brand', history: true)]
    public string $brand = '';

    #[Url]
    public string $sort = 'default';

    #[Url]
    public int $perPage = 12;

    #[Url]
    public int $page = 1;

    public array $allProducts = [];
    public array $categories = [];
    public array $brands = [];

    public function mount(SupplementsAggregatorService $supplements)
    {
        $products = collect($supplements->getProducts())
            ->filter(fn($p) => $p['title'])
            ->values();

        $this->allProducts = $products->toArray();

        $this->categories = $supplements->getCategories();
        $this->brands = $supplements->getBrands();
    }

    public function toggleCategory($slug)
    {
        $selected = explode(',', $this->category);

        if (in_array($slug, $selected)) {
            $selected = array_diff($selected, [$slug]);
        } else {
            $selected[] = $slug;
        }

        $this->category = implode(',', array_filter($selected));
        $this->page = 1;
    }

    public function toggleBrand($slug)
    {
        $selected = explode(',', $this->brand);

        if (in_array($slug, $selected)) {
            $selected = array_diff($selected, [$slug]);
        } else {
            $selected[] = $slug;
        }

        $this->brand = implode(',', array_filter($selected));
        $this->page = 1;
    }

    public function updatedSort()
    {
        $this->page = 1;
    }
    public function updatedPerPage()
    {
        $this->page = 1;
    }

    public function getFilteredProducts()
    {
        $products = collect($this->allProducts);

        if ($this->category) {
            $categories = explode(',', $this->category);
            $products = $products->filter(
                fn($p) =>
                in_array(Str::slug($p['category'] ?? ''), $categories)
            );
        }

        if ($this->brand) {
            $brands = explode(',', $this->brand);
            $products = $products->filter(
                fn($p) =>
                in_array(Str::slug($p['brand_name'] ?? ''), $brands)
            );
        }

        return match ($this->sort) {
            'price_asc' => $products->sortBy('price')->values(),
            'price_desc' => $products->sortByDesc('price')->values(),
            default => $products->values(),
        };
    }

    public function addToCart($productId)
    {
        $cart = session()->get('cart', []);

        $product = collect($this->allProducts)->firstWhere('id', $productId);

        if (!$product) return;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id'       => $product['id'],
                'name'     => $product['title'],
                'price'    => $product['price'],
                'currency' => $product['currency_symbol'] ?? 'лв.',
                'quantity' => 1,
                'image'    => $product['image'],
                'slug'     => $product['slug'] ?? null,
                'weight'   => $product['weight'] ?? null,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
    }


    public function render()
    {
        $products = $this->getFilteredProducts();

        $paginated = $products
            ->forPage($this->page, $this->perPage)
            ->values()
            ->toArray();

        return view('livewire.pages.catalog', [
            'products' => $paginated,
            'total' => $products->count(),
            'page' => $this->page,
            'perPage' => $this->perPage,
            'title' => $this->category || $this->brand ? 'Филтрирани продукти' : 'Всички продукти',
        ])->layout('layouts.app');
    }
}
