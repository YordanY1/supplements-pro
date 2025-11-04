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

    protected function normalizeRevitaProduct(array $p): ?array
    {
        $title = $p['name'] ?? $p['Short_Description'] ?? null;
        if (!$title) return null;

        $regular = $p['Regular_price'] ?? 0;
        $sale = $p['Sale_price'] ?? 0;

        return [
            'id'    => $p['upc'] ?? $p['SKU'] ?? md5($title),
            'title' => $title,
            'brand_name' => $p['Brand'] ?? 'Неизвестен бранд',
            'category' => $p['Category'] ?? null,

            // Price + old price
            'price' => (float)($sale > 0 ? $sale : $regular),
            'old_price' => $sale > 0 ? (float)$regular : null,

            // Image
            'image' => $p['images'][0] ?? $p['Image_URL'] ?? null,

            // Description
            'description' => $p['Long_Description']
                ?? $p['Short_Description']
                ?? null,

            // Supplement Facts Label
            'label' => $p['Label_Image']
                ?? ($p['images'][1] ?? null)
                ?? null,

            'currency_symbol' => 'лв.',
            'slug' => Str::slug($title),
            'source' => 'revita',
        ];
    }


    protected function normalizeFitness1Product(array $p): array
    {
        $regular = $p['regular_price'] ?? 0;
        $sale = $p['sale_price'] ?? 0;

        return [
            'id' => $p['id'] ?? $p['product_id'],
            'title' => $p['product_name'] ?? null,
            'brand_name' => $p['brand_name'] ?? null,
            'category' => explode(' > ', $p['category'])[0] ?? null,
            'price' => (float)($sale > 0 ? $sale : $regular),
            'old_price' => $sale > 0 ? (float)$regular : null,
            'image' => $p['image'] ?? null,
            'currency_symbol' => 'лв.',
            'slug' => Str::slug($p['product_name'] ?? ''),
            'source' => 'fitness1',
        ];
    }


    protected function loadProducts(): array
    {
        return Cache::remember('supplements.all_products', now()->addMinutes(10), function () {

            $fitnessProducts = collect($this->fitness->getProducts())
                ->map(fn($p) => $this->normalizeFitness1Product($p));

            $revitaProducts = collect($this->revita->getProducts())
                ->map(fn($p) => $this->normalizeRevitaProduct($p))
                ->filter();

            $existingSlugs = $fitnessProducts->pluck('slug')->toArray();

            $filteredRevita = $revitaProducts->reject(
                fn($p) =>
                in_array($p['slug'], $existingSlugs)
            );

            return $fitnessProducts->merge($filteredRevita)
                ->values()
                ->toArray();
        });
    }

    public function getProducts(): array
    {
        return $this->productsCache ??= $this->loadProducts();
    }

    public function getCategories(): array
    {
        return Cache::remember('supplements.categories', now()->addMinutes(10), function () {
            return collect($this->getProducts())
                ->pluck('category')
                ->filter()
                ->unique()
                ->map(fn($cat) => [
                    'name' => $cat,
                    'slug' => Str::slug($cat),
                ])
                ->values()
                ->toArray();
        });
    }

    public function getBrands(): array
    {
        return Cache::remember('supplements.brands', now()->addMinutes(10), function () {
            return collect($this->getProducts())
                ->pluck('brand_name')
                ->filter()
                ->unique()
                ->map(fn($brand) => [
                    'name' => $brand,
                    'slug' => Str::slug($brand),
                ])
                ->values()
                ->toArray();
        });
    }

    public function getProductById($id): ?array
    {
        return collect($this->getProducts())
            ->firstWhere('id', $id);
    }
}
