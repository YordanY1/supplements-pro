<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Services\SupplementsAggregatorService;

class Product extends Component
{
    public array $product;

    public function mount($slug, SupplementsAggregatorService $sup)
    {
        $product = collect($sup->getProducts())
            ->first(fn($p) => $p['slug'] === $slug);

        abort_unless($product, 404);

        $this->product = $product;
    }

    public function addToCart()
    {
        $cart = session()->get('cart', []);

        $id = $this->product['id'];

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id' => $this->product['id'],
                'name' => $this->product['title'],
                'price' => $this->product['price'],
                'currency' => $this->product['currency_symbol'],
                'quantity' => 1,
                'image' => $this->product['image'],
                'slug' => $this->product['slug'],
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
