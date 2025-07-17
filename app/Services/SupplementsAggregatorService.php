<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SupplementsAggregatorService
{
    protected Fitness1Service $fitness;
    protected RevitaService $revita;

    public function __construct(Fitness1Service $fitness, RevitaService $revita)
    {
        $this->fitness = $fitness;
        $this->revita = $revita;
    }


    public function getProducts(): array
    {
        return Cache::remember('supplements.all_products', now()->addMinutes(10), function () {
            $fitnessProducts = $this->fitness->getProducts();
            $revitaProducts = $this->revita->getProducts();

            $existingSlugs = collect($fitnessProducts)
                ->pluck('title')
                ->map(fn($title) => Str::slug($title))
                ->toArray();

            $filteredRevita = collect($revitaProducts)
                ->reject(fn($product) => in_array(Str::slug($product['title']), $existingSlugs));

            return collect($fitnessProducts)
                ->merge($filteredRevita)
                ->values()
                ->toArray();
        });
    }

    public function getCategories(): array
    {
        return Cache::remember('supplements.categories', now()->addMinutes(10), function () {
            $products = $this->getProducts();

            return collect($products)
                ->pluck('category')
                ->map(fn($cat) => explode(' > ', $cat)[0])
                ->filter()
                ->unique()
                ->sort()
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
            $products = $this->getProducts();

            return collect($products)
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
        });
    }

    public function getProductById($id): ?array
    {
        return collect($this->getProducts())
            ->firstWhere('id', $id);
    }

}
