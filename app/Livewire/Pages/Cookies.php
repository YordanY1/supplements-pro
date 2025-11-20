<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Cookies extends Component
{
    public function render()
    {
        return view('livewire.pages.cookies')->layout('layouts.app', [
            'title' => 'Политика за бисквитки – Holistica',
            'description' => 'Информация за използваните бисквитки и вашите права.',
            'robots' => 'index, follow',
        ]);
    }
}
