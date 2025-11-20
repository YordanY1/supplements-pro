<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        return view('livewire.pages.home')
            ->layout('layouts.app', [
                'title'       => 'Holistica – Хранителни добавки за ритъм, баланс и здраве',
                'description' => 'Holistica предлага качествени натурални хранителни добавки, витамини, минерали и продукти за по-добро здраве, енергия и хормонален баланс.',
                'image'       => asset('images/logo-removebg.jpg'),
                'robots'      => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
                'ogType'      => 'website',
                'organizationSchema' => [
                    '@type' => 'Organization',
                    'name' => 'Holistica',
                    'url'  => url('/'),
                    'logo' => asset('images/logo.png'),
                    'sameAs' => [
                        'https://instagram.com/',
                        'https://facebook.com/',
                        'https://tiktok.com/'
                    ],
                ],
                'websiteSchema' => [
                    '@type' => 'WebSite',
                    'name' => 'Holistica',
                    'url'  => url('/'),
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/search?q={query}'),
                        'query-input' => 'required name=query'
                    ]
                ],

            ]);
    }
}
