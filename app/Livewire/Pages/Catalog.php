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
        $this->categories = Product::query()
            ->whereNotNull('category_slug')
            ->select('category', 'category_slug')
            ->distinct()
            ->orderBy('category')
            ->get()
            ->map(fn($c) => ['name' => $c->category, 'slug' => $c->category_slug])
            ->toArray();

        $this->brands = Product::query()
            ->whereNotNull('brand_slug')
            ->select('brand_name', 'brand_slug')
            ->distinct()
            ->orderBy('brand_name')
            ->get()
            ->map(fn($b) => ['name' => $b->brand_name, 'slug' => $b->brand_slug])
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

        if ($this->category) {
            $q->whereIn('category_slug', explode(',', $this->category));
        }

        if ($this->brand) {
            $q->whereIn('brand_slug', explode(',', $this->brand));
        }

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
        $products = $this->query()->paginate($this->perPage, page: $this->page);

        $title = 'Каталог – Holistica';
        $description = 'Разгледайте натурални хранителни добавки, витамини и минерали от Holistica.';

        if ($this->category) {
            $categories = collect(explode(',', $this->category))
                ->map(fn($slug) => collect($this->categories)->firstWhere('slug', $slug)['name'] ?? null)
                ->filter()
                ->implode(', ');
            $title = "Категория: $categories – Holistica";
            $description = "Продукти в категория: $categories.";
        }

        if ($this->brand) {
            $brands = collect(explode(',', $this->brand))
                ->map(fn($slug) => collect($this->brands)->firstWhere('slug', $slug)['name'] ?? null)
                ->filter()
                ->implode(', ');
            $title = "Бранд: $brands – Holistica";
            $description = "Продукти от бранд: $brands.";
        }

        return view('livewire.pages.catalog', [
            'products'    => $products->items(),
            'total'       => $products->total(),
            'page'        => $this->page,
            'perPage'     => $this->perPage,
            'title'       => $title,
            'description' => $description,
        ])->layout('layouts.app', [
            'title'       => $title,
            'description' => $description,
            'image'       => asset('images/logo-removebg.jpg'),
            'robots'      => 'index, follow',
            'ogType'      => 'website',

            'breadcrumb' => [
                ['name' => 'Начало', 'url' => url('/')],
                ['name' => 'Каталог', 'url' => url('/catalog')],
            ],

            'itemListSchema' => [
                '@type' => 'ItemList',
                'name'  => 'Holistica Catalog',
                'itemListElement' => collect($products->items())->values()->map(function ($p, $i) {
                    return [
                        '@type'     => 'ListItem',
                        'position'  => $i + 1,
                        'name'      => $p->title,
                        'url'       => url('/product/' . $p->slug),
                    ];
                }),
            ],

            'organizationSchema' => [
                '@type' => 'Organization',
                'name'  => 'Holistica',
                'url'   => url('/'),
                'logo'  => asset('images/logo-removebg.jpg'),
            ],
        ]);
    }
}
