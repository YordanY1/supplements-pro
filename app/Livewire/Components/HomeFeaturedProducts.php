<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Services\SupplementsAggregatorService;

class HomeFeaturedProducts extends Component
{
    public array $products = [];

    public function mount(SupplementsAggregatorService $aggregator)
    {
        $this->products = collect($aggregator->getProducts())
            ->shuffle()
            ->take(3)
            ->values()
            ->toArray();
    }

    public function addToCart(string $productId)
    {
        $cart = session()->get('cart', []);

        $product = collect($this->products)->firstWhere('id', $productId);

        if (! $product) return;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id'       => $product['id'],
                'name'     => $product['title'],
                'price'    => $product['price'],
                'currency' => $product['currency_symbol'] ?? 'лв.',
                'quantity' => 1,
                'image'    => $product['image'],
                'slug'     => $product['slug'],
                'weight'   => $product['weight'] ?? null,
                'source'   => $product['source'] ?? null,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.components.home-featured-products', [
            'products' => $this->products,
        ]);
    }
}
