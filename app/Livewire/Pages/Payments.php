<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Payments extends Component
{
    public function render()
    {
        return view('livewire.pages.payments')->layout('layouts.app', [
            'title' => 'Политика за плащане – Holistica',
            'description' => 'Приемани методи на плащане и условия за обработка.',
            'robots' => 'index, follow',
        ]);
    }
}
