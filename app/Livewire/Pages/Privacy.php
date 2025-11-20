<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Privacy extends Component
{
    public function render()
    {
        return view('livewire.pages.privacy')->layout('layouts.app', [
            'title' => 'Политика за поверителност – Holistica',
            'description' => 'Политика за поверителност и обработка на лични данни според GDPR.',
            'robots' => 'index, follow',
            'image' => asset('images/logo-removebg.jpg'),
        ]);
    }
}
