<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Services\DatabaseProductsService;

class Categories extends Component
{
    public array $categories = [];

    public function mount(DatabaseProductsService $db)
    {
        $this->categories = $db->getCategories();
    }

    public function render()
    {
        return view('livewire.pages.categories')->layout('layouts.app', [
            'title'       => 'Категории продукти – Holistica',
            'description' => 'Разгледайте всички категории хранителни добавки, витамини и минерали, налични в Holistica.',
            'image'       => asset('images/logo-removebg.jpg'),
            'robots'      => 'index, follow',
            'ogType'      => 'website',

            'breadcrumb' => [
                ['name' => 'Начало', 'url' => url('/')],
                ['name' => 'Категории', 'url' => url('/categories')],
            ],

            'itemListSchema' => [
                '@type' => 'ItemList',
                'name'  => 'Holistica Categories',
                'itemListElement' => collect($this->categories)->values()->map(function ($cat, $i) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $i + 1,
                        'name' => $cat['name'],
                        'url'  => url('/category/' . ($cat['slug'] ?? str()->slug($cat['name']))),
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
