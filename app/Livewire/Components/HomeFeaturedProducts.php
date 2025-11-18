<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Product;

class HomeFeaturedProducts extends Component
{
    public array $products = [];

    public function mount()
    {
        $this->products = Product::inRandomOrder()
            ->limit(3)
            ->get()
            ->map(function ($p) {
                return [
                    'id'         => $p->id,
                    'vendor_id'  => $p->vendor_id,
                    'title'      => $p->title,
                    'slug'       => $p->slug,
                    'brand_name' => $p->brand_name,
                    'category'   => $p->category,
                    'price'      => $p->price,
                    'currency'   => 'лв.',
                    'image'      => $p->image,
                    'weight'     => null,
                    'source'     => $p->source,
                ];
            })
            ->toArray();
    }

    public function addToCart(string $productId)
    {
        $cart = session()->get('cart', []);

        $product = \App\Models\Product::find($productId);
        if (! $product) return;

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
        return view('livewire.components.home-featured-products', [
            'products' => $this->products,
        ]);
    }
}
