<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Fitness1Service
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.fitness1.base_url');
        $this->apiKey = config('services.fitness1.key');
    }

    public function getProducts(bool $withDescriptions = false): array
    {
        $cacheKey = $withDescriptions ? 'fitness1.products.with_desc' : 'fitness1.products';

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($withDescriptions) {
            $params = ['key' => $this->apiKey];
            if ($withDescriptions) {
                $params['description'] = 1;
            }

            $response = Http::get($this->baseUrl, $params);

            if ($response->successful()) {
                return $response->json()['products'] ?? [];
            }

            return [];
        });
    }

    public function getProductById($id): ?array
    {
        return collect($this->getProducts())->firstWhere('id', $id);
    }

    public function getCategories(): array
    {
        return collect($this->getProducts())
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
