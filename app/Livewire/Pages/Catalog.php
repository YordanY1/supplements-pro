<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Product;

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

    public array $categories = [];
    public array $brands = [];


    public function mount()
    {
        // Categories from DB
        $this->categories = Product::query()
            ->whereNotNull('category_slug')
            ->select('category', 'category_slug')
            ->distinct()
            ->orderBy('category')
            ->get()
            ->map(fn($c) => [
                'name' => $c->category,
                'slug' => $c->category_slug,
            ])
            ->toArray();

        // Brands from DB
        $this->brands = Product::query()
            ->whereNotNull('brand_slug')
            ->select('brand_name', 'brand_slug')
            ->distinct()
            ->orderBy('brand_name')
            ->get()
            ->map(fn($b) => [
                'name' => $b->brand_name,
                'slug' => $b->brand_slug,
            ])
            ->toArray();
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


    protected function query()
    {
        $q = Product::query();

        // Category filter using category_slug
        if ($this->category) {
            $q->whereIn('category_slug', explode(',', $this->category));
        }

        // Brand filter using brand_slug
        if ($this->brand) {
            $q->whereIn('brand_slug', explode(',', $this->brand));
        }

        // Sorting
        match ($this->sort) {
            'price_asc'  => $q->orderBy('price', 'asc'),
            'price_desc' => $q->orderBy('price', 'desc'),
            default      => $q->orderBy('id', 'desc'),
        };

        return $q;
    }


    public function updatedSort()
    {
        $this->page = 1;
    }

    public function updatedPerPage()
    {
        $this->page = 1;
    }


    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (! $product) return;

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id'       => $product->id,
                'name'     => $product->title,
                'price'    => (float)$product->price,
                'currency' => 'лв.',
                'quantity' => 1,
                'image'    => $product->image,
                'slug'     => $product->slug,
                'weight'   => $product->weight ?? null,
                'source'   => $product->source ?? null,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
    }


    public function render()
    {
        $products = $this->query()
            ->paginate($this->perPage, page: $this->page);

        return view('livewire.pages.catalog', [
            'products' => $products->items(),
            'total'    => $products->total(),
            'page'     => $this->page,
            'perPage'  => $this->perPage,
            'title'    => ($this->category || $this->brand)
                ? 'Филтрирани продукти'
                : 'Всички продукти',
        ])->layout('layouts.app');
    }
}
