<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RevitaService
{

    public function getProducts(): array
    {
        $response = Http::get(config('services.revita.url'));

        if (!$response->successful()) {
            return [];
        }

        $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);
        $data = json_decode($json, true);

        $rawProducts = $data['product'] ?? [];

        $products = $this->isAssoc($rawProducts) ? [$rawProducts] : $rawProducts;


        return collect($products)
            ->map(function ($product) {
                $rawBrand = $product['brand'] ?? $product['Brand'] ?? null;
                $brand = $this->extractValue($rawBrand);

                return [
                    'id' => $product['id'] ?? Str::uuid()->toString(),
                    'title' => $product['name'] ?? 'Без име',
                    'brand_name' => $brand ?: null,
                    'category' => $product['category'] ?? 'Неуточнена',
                    'price' => $product['price'] ?? null,
                    'source' => 'revita',
                ];
            })
            ->filter(fn($p) => $p['brand_name'])
            ->toArray();
    }

    private function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function extractValue($value): ?string
    {
        if (is_string($value)) {
            return trim($value);
        }

        if (is_array($value)) {
            if (empty($value)) {
                return null;
            }

            if (isset($value['#cdata-section'])) {
                return $this->extractValue($value['#cdata-section']);
            }

            foreach ($value as $val) {
                $result = $this->extractValue($val);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }
}
