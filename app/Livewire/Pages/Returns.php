<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Returns extends Component
{
    public function render()
    {
        return view('livewire.pages.returns')->layout('layouts.app', [
            'title' => 'Политика за връщане и отказ – Holistica',
            'description' => 'Правила за връщане на продукти и отказ от договор.',
            'robots' => 'index, follow',
        ]);
    }
}
