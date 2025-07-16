<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Services\Fitness1Service;



class Header extends Component
{
    public $categories = [];
    public $brands = [];

    public function mount(Fitness1Service $fitness)
    {
        $this->categories = $fitness->getCategories();
        $this->brands = $fitness->getBrands();
    }


    public function render()
    {
        return view('livewire.components.header', [
            'categories' => $this->categories,
            'brands' => $this->brands,
        ]);
    }
}
