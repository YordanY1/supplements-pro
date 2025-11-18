<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class DatabaseProductsService
{
    public function getCategories(): array
    {
        return Product::query()
            ->whereNotNull('category_slug')
            ->select('category', 'category_slug')
            ->distinct()
            ->orderBy('category')
            ->get()
            ->map(fn($c) => [
                'name' => $c->category,
                'slug' => $c->category_slug,
            ])
            ->toArray();
    }

    public function getBrands(): array
    {
        return Product::query()
            ->whereNotNull('brand_slug')
            ->select('brand_name', 'brand_slug')
            ->distinct()
            ->orderBy('brand_name')
            ->get()
            ->map(fn($b) => [
                'name' => $b->brand_name,
                'slug' => $b->brand_slug,
            ])
            ->toArray();
    }

    public function getProducts(array $filters = [])
    {
        $query = Product::query();

        if (!empty($filters['category'])) {
            $query->whereIn('category_slug', explode(',', $filters['category']));
        }

        if (!empty($filters['brand'])) {
            $query->whereIn('brand_slug', explode(',', $filters['brand']));
        }

        return $query->paginate($filters['perPage'] ?? 12);
    }
}
