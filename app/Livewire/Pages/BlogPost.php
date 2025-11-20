<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\BlogPost as BlogPostModel;

class BlogPost extends Component
{
    public $post;

    public function mount($slug)
    {
        $this->post = BlogPostModel::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.pages.blog-post')->layout('layouts.app', [
            'title'             => $this->post->title,
            'description'       => $this->post->excerpt,
            'image'             => $this->post->image ?? asset('images/logo-removebg.jpg'),
            'ogType'            => 'article',
            'robots'            => 'index, follow',
            'breadcrumb' => [
                ['name' => 'Начало', 'url' => url('/')],
                ['name' => 'Блог',   'url' => url('/blog')],
                ['name' => $this->post->title, 'url' => url('/blog/' . $this->post->slug)],
            ],
            'articleSchema' => [
                '@type' => 'Article',
                'headline' => $this->post->title,
                'description' => $this->post->excerpt,
                'image' => [$this->post->image ?? asset('images/logo-removebg.jpg')],
                'author' => $this->post->author ?? 'Holistica',
                'datePublished' => $this->post->created_at->toIso8601String(),
                'dateModified' => $this->post->updated_at->toIso8601String(),
                'mainEntityOfPage' => url('/blog/' . $this->post->slug),
            ],
        ]);
    }
}
