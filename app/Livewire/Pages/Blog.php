<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\BlogPost;
use Livewire\WithPagination;

class Blog extends Component
{
    use WithPagination;

    public function render()
    {
        $posts = BlogPost::latest()->paginate(9);

        return view('livewire.pages.blog', [
            'posts' => $posts,
        ])->layout('layouts.app', [

            'title' => 'Holistica Блог – Статии за здраве, добавки и хормонален баланс',
            'description' => 'Прочети статии за витамини, минерали, здравословно хранене, хормонален баланс и натурални решения за по-добър живот.',
            'image' => asset('images/logo-removebg.jpg'),
            'ogType' => 'website',
            'robots' => 'index, follow',

            'breadcrumb' => [
                ['name' => 'Начало', 'url' => url('/')],
                ['name' => 'Блог', 'url' => url('/blog')],
            ],

            'itemListSchema' => [
                '@type' => 'ItemList',
                'name' => 'Holistica Blog',
                'itemListElement' => $posts->map(function ($post, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $post->title,
                        'url' => url('/blog/' . $post->slug),
                    ];
                }),
            ],

            'organizationSchema' => [
                '@type' => 'Organization',
                'name' => 'Holistica',
                'url' => url('/'),
                'logo' => asset('images/logo.png'),
                'sameAs' => [
                    'https://facebook.com/',
                    'https://instagram.com/',
                    'https://tiktok.com/',
                ]
            ],
        ]);
    }
}
