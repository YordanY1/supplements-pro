<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SupplementsAggregatorService
{
    protected Fitness1Service $fitness;
    protected RevitaService $revita;

    protected ?array $productsCache = null;

    public function __construct(Fitness1Service $fitness, RevitaService $revita)
    {
        $this->fitness = $fitness;
        $this->revita = $revita;
    }

    protected function normalizeFitness1Product(array $p): array
    {
        $regular = $p['regular_price'] ?? 0;
        $sale    = $p['sale_price'] ?? 0;

        return [
            'id' => $p['id'] ?? $p['product_id'],
            'title' => $p['product_name'] ?? null,
            'slug'  => Str::slug($p['product_name'] ?? ''),

            'brand_name' => $p['brand_name'] ?? null,
            'category' => explode(' > ', $p['category'])[0] ?? null,

            'price'     => (float)($sale > 0 ? $sale : $regular),
            'old_price' => $sale > 0 ? (float)$regular : null,
            'stock'     => $p['available'] ? 1 : 0,

            'source' => 'fitness1',

            'image'  => $p['image'] ?? null,
            'images' => [$p['image'] ?? null],

            'sku' => $p['product_id'] ?? null,
            'upc' => $p['barcode'] ?? null,
            'ean' => null,
        ];
    }

    protected function loadProducts(): array
    {
        $fitness = collect($this->fitness->getProducts())
            ->map(fn($p) => $this->normalizeFitness1Product($p));

        $revita = collect($this->revita->getProducts());

        $fitnessKeys = $fitness
            ->map(fn($p) => $p['upc'] ?? $p['sku'] ?? $p['slug'])
            ->filter()
            ->unique()
            ->toArray();

        $revita = $revita->reject(
            fn($p) => in_array($p['upc'] ?? $p['sku'] ?? $p['slug'], $fitnessKeys)
        );

        return $fitness->merge($revita)->values()->toArray();
    }


    public function getProducts(): array
    {
        return $this->productsCache ??= $this->loadProducts();
    }

    public function getCategories(): array
    {
        return collect($this->getProducts())
            ->pluck('category')
            ->filter()
            ->unique()
            ->sort()
            ->map(fn($cat) => [
                'name' => $cat,
                'slug' => Str::slug($cat),
            ])
            ->values()
            ->toArray();
    }

    public function getBrands(): array
    {
        return collect($this->getProducts())
            ->pluck('brand_name')
            ->filter()
            ->unique()
            ->sort()
            ->map(fn($brand) => [
                'name' => $brand,
                'slug' => Str::slug($brand),
            ])
            ->values()
            ->toArray();
    }
}
