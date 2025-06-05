<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
    public array $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    #[On('cart-updated')]
    public function updateCart()
    {
        $this->cart = session()->get('cart', []);
    }

    public function remove(int $productId): void
    {
        unset($this->cart[$productId]);
        session()->put('cart', $this->cart);
    }

    public function increment(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            session()->put('cart', $this->cart);
        }
    }

    public function decrement(int $productId): void
    {
        if (isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
            session()->put('cart', $this->cart);
        }
    }

    public function getTotalProperty(): float
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function render()
    {
        return view('livewire.pages.cart')
            ->layout('layouts.app', ['title' => 'Cart']);
    }
}
