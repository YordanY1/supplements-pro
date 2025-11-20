<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Services\DatabaseProductsService;

class Brands extends Component
{
    public array $brands = [];

    public function mount(DatabaseProductsService $db)
    {
        $this->brands = $db->getBrands();
    }

    public function render()
    {
        return view('livewire.pages.brands')->layout('layouts.app', [
            'title'       => 'Брандове – Holistica',
            'description' => 'Разгледайте всички брандове с висококачествени хранителни добавки, витамини и минерали в Holistica.',
            'image'       => asset('images/logo-removebg.jpg'),
            'robots'      => 'index, follow',
            'ogType'      => 'website',

            'breadcrumb' => [
                ['name' => 'Начало', 'url' => url('/')],
                ['name' => 'Брандове', 'url' => url('/brands')],
            ],

            'itemListSchema' => [
                '@type' => 'ItemList',
                'name'  => 'Holistica Brands',
                'itemListElement' => collect($this->brands)->values()->map(function ($brand, $i) {
                    return [
                        '@type'    => 'ListItem',
                        'position' => $i + 1,
                        'name'     => $brand['name'],
                        'url'      => url('/brand/' . ($brand['slug'] ?? str()->slug($brand['name']))),
                    ];
                }),
            ],

            'organizationSchema' => [
                '@type' => 'Organization',
                'name'  => 'Holistica',
                'url'   => url('/'),
                'logo'  => asset('images/logo-removebg.jpg'),
            ],
        ]);
    }
}
