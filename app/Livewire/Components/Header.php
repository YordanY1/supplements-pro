<?php

namespace App\Livewire\Components;

use App\Models\Category;
use App\Models\Brand;
use Livewire\Component;

class Header extends Component
{
    public function render()
    {
        return view('livewire.components.header', [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }
}
