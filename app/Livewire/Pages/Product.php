<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Services\SupplementsAggregatorService;

class Product extends Component
{
    public array $product;

    public function mount($slug)
    {
        $product = \App\Models\Product::where('slug', $slug)->firstOrFail();
        $this->product = $product->toArray();
    }


    public function addToCart()
    {
        $cart = session()->get('cart', []);

        $id = $this->product['id'];

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id'       => $this->product['id'],
                'name'     => $this->product['title'],
                'price'    => (float)$this->product['price'],
                'currency' => 'лв.',
                'quantity' => 1,
                'image'    => $this->product['image'],
                'slug'     => $this->product['slug'],
                'weight'   => $this->product['weight'] ?? null,
                'source'   => $this->product['source'] ?? null,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
    }



    public function render()
    {
        return view('livewire.pages.product')->layout('layouts.app');
    }
}
