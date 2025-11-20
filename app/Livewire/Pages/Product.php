<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Product as ProductModel;

class Product extends Component
{
    public array $product;

    public function mount($slug)
    {
        $model = ProductModel::where('slug', $slug)->firstOrFail();

        $this->product = [
            'id'                    => $model->id,
            'title'                 => $model->title,
            'slug'                  => $model->slug,
            'brand_name'            => $model->brand_name,
            'category'              => $model->category,
            'price'                 => $model->price,
            'old_price'             => $model->old_price,
            'image'                 => $model->image,
            'images'                => is_array($model->images)
                ? $model->images
                : (json_decode($model->images ?? '[]', true) ?? []),
            'description_html'      => $model->description_html,
            'supplement_facts_html' => $model->supplement_facts_html,
            'label'                 => $model->label,
            'weight'                => $model->weight,
            'source'                => $model->source,
            'pack'                  => $model->pack ?? null,
            'available'             => $model->stock > 0,
        ];
    }

    public function addToCart()
    {
        $cart = session()->get('cart', []);
        $id = $this->product['id'];

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id'       => $id,
                'name'     => $this->product['title'],
                'price'    => (float)$this->product['price'],
                'currency' => 'лв.',
                'quantity' => 1,
                'image'    => $this->product['image'],
                'slug'     => $this->product['slug'],
                'weight'   => $this->product['weight'],
                'source'   => $this->product['source'],
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $p = $this->product;

        return view('livewire.pages.product', [
            'product' => $p,
        ])->layout('layouts.app', [
            'title'       => $p['title'] . ' – Holistica',
            'description' => strip_tags(substr($p['description_html'] ?? '', 0, 180)),
            'image'       => $p['image'] ?? asset('images/logo-removebg.jpg'),
            'robots'      => 'index, follow',
            'ogType'      => 'product',

            'breadcrumb' => [
                ['name' => 'Начало', 'url' => url('/')],
                ['name' => 'Каталог', 'url' => url('/catalog')],
                ['name' => $p['title'], 'url' => url('/product/' . $p['slug'])],
            ],

            'productSchema' => [
                '@type' => 'Product',
                'name'  => $p['title'],
                'image' => array_merge([$p['image']], $p['images']),
                'description' => strip_tags($p['description_html']),
                'brand' => [
                    '@type' => 'Brand',
                    'name'  => $p['brand_name'] ?? 'Holistica',
                ],
                'category' => $p['category'] ?? null,
                'sku'      => $p['id'],
                'offers' => [
                    '@type'           => 'Offer',
                    'url'             => url('/product/' . $p['slug']),
                    'priceCurrency'   => 'BGN',
                    'price'           => $p['price'],
                    'availability'    => $p['available']
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'itemCondition'   => 'https://schema.org/NewCondition',
                ],
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
