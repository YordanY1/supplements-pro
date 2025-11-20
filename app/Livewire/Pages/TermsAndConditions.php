<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class TermsAndConditions extends Component
{
    public function render()
    {
        return view('livewire.pages.terms-and-conditions')->layout('layouts.app', [
            'title' => 'Общи условия – Holistica',
            'description' => 'Общи условия за ползване и пазаруване в онлайн магазина Holistica.',
            'robots' => 'index, follow',
        ]);
    }
}
