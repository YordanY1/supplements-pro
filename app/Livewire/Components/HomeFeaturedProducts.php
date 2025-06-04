<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Product;

class HomeFeaturedProducts extends Component
{
    public function render()
    {
        return view('livewire.components.home-featured-products', [
            'products' => Product::with('brand')->latest()->take(3)->get(),
        ]);
    }
}
