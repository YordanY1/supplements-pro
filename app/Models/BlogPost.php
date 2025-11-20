<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image',
        'categories',
        'tags',
        'excerpt',
        'content',
        'published',
        'author',
    ];

    protected $casts = [
        'categories' => 'array',
        'tags'       => 'array',
        'published'  => 'boolean',
    ];
}
