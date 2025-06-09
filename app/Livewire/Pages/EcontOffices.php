<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Gdinko\Econt\Facades\Econt;

class EcontOffices extends Component
{
    public array $offices = [];

    public function mount()
    {
        $this->offices = collect(Econt::getOffices())
            ->filter(
                fn($office) =>
                isset($office['address']['city']['country']['code2']) &&
                    $office['address']['city']['country']['code2'] === 'BG'
            )
            ->sortBy(fn($office) => $office['address']['city']['name'] ?? '')
            ->values()
            ->toArray();
    }


    public function render()
    {
        return view('livewire.pages.econt-offices')->layout('layouts.app');
    }
}
