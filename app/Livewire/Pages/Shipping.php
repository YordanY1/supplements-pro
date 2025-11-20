<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Shipping extends Component
{
    public function render()
    {
        return view('livewire.pages.shipping')->layout('layouts.app', [
            'title' => 'Политика за доставка – Holistica',
            'description' => 'Информация за доставка, срокове и куриери.',
            'robots' => 'index, follow',
        ]);
    }
}
