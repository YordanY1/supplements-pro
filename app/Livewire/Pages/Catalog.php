<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
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
        // Categories
        $this->categories = Product::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->get()
            ->map(fn($c) => [
                'name' => $c->category,
                'slug' => Str::slug($c->category),
            ])
            ->toArray();

        // Brands
        $this->brands = Product::query()
            ->whereNotNull('brand_name')
            ->select('brand_name')
            ->distinct()
            ->get()
            ->map(fn($b) => [
                'name' => $b->brand_name,
                'slug' => Str::slug($b->brand_name),
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

        // Category filter
        if ($this->category) {
            $categories = explode(',', $this->category);

            $q->where(function ($q) use ($categories) {
                foreach ($categories as $cat) {
                    $q->orWhereRaw(
                        "REPLACE(LOWER(category), ' ', '-') = ?",
                        [$cat]
                    );
                }
            });
        }


        // Brand filter
        if ($this->brand) {
            $brands = explode(',', $this->brand);
            $q->where(function ($q) use ($brands) {
                foreach ($brands as $b) {
                    $q->orWhereRaw('LOWER(REPLACE(brand_name, " ", "-")) = ?', [$b]);
                }
            });
        }

        // Sort
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
            'total' => $products->total(),
            'page' => $this->page,
            'perPage' => $this->perPage,
            'title' => ($this->category || $this->brand) ? 'Филтрирани продукти' : 'Всички продукти',
        ])->layout('layouts.app');
    }
}
