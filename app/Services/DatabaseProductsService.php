<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class DatabaseProductsService
{

    private function resolveBrandImage(string $brandName): ?string
    {
        $dir = public_path('images/brands');
        $files = scandir($dir);

        $keyword = strtolower(preg_replace('/[^A-Za-z0-9]/', '', explode(' ', $brandName)[0]));

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) continue;

            $name = strtolower(pathinfo($file, PATHINFO_FILENAME));

            if (str_starts_with($name, $keyword)) {
                return $file;
            }
        }

        return null;
    }



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
        $brands = Product::query()
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

        return collect($brands)
            ->map(function ($brand) {
                return [
                    'name'  => $brand['name'],
                    'slug'  => $brand['slug'],
                    'image' => $this->resolveBrandImage($brand['name']),
                ];
            })
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
