<?php

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class CartBadge extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->updateCount();
    }

    #[On('cart-updated')]
    public function updateCount(): void
    {
        $cart = session()->get('cart', []);
        $this->count = collect($cart)->sum('quantity');
    }

    public function render()
    {
        return view('livewire.components.cart-badge');
    }
}
