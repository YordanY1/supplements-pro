<?php

namespace App\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class HomeFeaturedProducts extends Component
{
    public function addToCart(int $productId)
    {
        $product = Product::with('currency')->findOrFail($productId);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'currency' => $product->currency->symbol ?? 'лв.',
                'quantity' => 1,
                'image' => $product->image,
                'slug' => $product->slug,
                'weight' => $product->weight,
            ];
        }

        session()->put('cart', $cart);

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.components.home-featured-products', [
            'products' => Product::with('brand')->latest()->take(3)->get(),
        ]);
    }
}
