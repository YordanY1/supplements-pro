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
            'images' => is_array($model->images)
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
        return view('livewire.pages.product')
            ->layout('layouts.app');
    }
}
