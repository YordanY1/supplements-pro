<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RevitaService
{
    const CACHE_FILE = 'revita.json';
    const CACHE_TTL_HOURS = 12;

    public function getProducts(): array
    {
        if ($this->cacheExistsAndFresh()) {
            return json_decode(Storage::get(self::CACHE_FILE), true);
        }

        $response = Http::timeout(30)->get(config('services.revita.url'));

        if (!$response->successful()) {
            return $this->getCachedOrEmpty();
        }

        $xml  = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $data = json_decode(json_encode($xml), true);

        $raw = $data['product'] ?? [];
        $products = $this->isAssoc($raw) ? [$raw] : $raw;

        $normalized = collect($products)->map(function ($p) {
            $title = trim($p['name'] ?? $p['Short_Description'] ?? null);

            if (!$title) {
                return null;
            }

            $regular = (float) ($p['Regular_price'] ?? 0);
            $sale    = (float) ($p['Sale_price'] ?? 0);
            $price   = $sale > 0 ? $sale : $regular;

            $images = [];
            if (!empty($p['images']['Image'])) {
                $imgs = $p['images']['Image'];
                $images = is_array($imgs) ? $imgs : [$imgs];
            }

            return [
                'id'       => $p['upc'] ?? $p['EA_number'] ?? $p['SKU'] ?? md5($title),
                'title'    => $title,
                'slug'     => Str::slug($title),
                'brand_name' => $p['Brand'] ?? 'Неизвестен',
                'category' => $p['Category'] ?? 'Други',

                'price'     => $price ?: null,
                'old_price' => $sale > 0 ? $regular : null,
                'stock'     => (int) ($p['InStock'] ?? 0),

                'source' => 'revita',

                'image'  => $images[0] ?? null,
                'images' => $images,

                'short_description' => $p['Short_Description'] ?? null,
                'description_html'  => is_array($p['description'] ?? null)
                    ? json_encode($p['description'])
                    : ($p['description'] ?? null),

                'supplement_facts_html' => null,

                'sku' => $p['SKU'] ?? null,
                'upc' => $p['upc'] ?? null,
                'ean' => $p['EA_number'] ?? null,
            ];
        })
            ->filter()
            ->values()
            ->toArray();

        Storage::put(self::CACHE_FILE, json_encode($normalized));

        return $normalized;
    }

    private function cacheExistsAndFresh(): bool
    {
        if (!Storage::exists(self::CACHE_FILE)) {
            return false;
        }

        $last = Carbon::createFromTimestamp(Storage::lastModified(self::CACHE_FILE));

        return $last->greaterThan(now()->subHours(self::CACHE_TTL_HOURS));
    }

    private function getCachedOrEmpty(): array
    {
        return Storage::exists(self::CACHE_FILE)
            ? json_decode(Storage::get(self::CACHE_FILE), true)
            : [];
    }

    private function isAssoc(array $a): bool
    {
        return array_keys($a) !== range(0, count($a) - 1);
    }
}
