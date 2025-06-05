<?php

namespace App\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class HomeFeaturedProducts extends Component
{
    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image ?? 'images/default.jpg',
                'quantity' => 1,
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
