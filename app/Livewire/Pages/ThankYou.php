<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class ThankYou extends Component
{
    public function render()
    {

        return view('livewire.pages.thank-you')
            ->layout('layouts.app');
    }
}
