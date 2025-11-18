<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupplementsAggregatorService;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Str;

class SyncProducts extends Command
{
    protected $signature = 'products:sync';
    protected $description = 'Sync supplements into single products table';

    public function handle(SupplementsAggregatorService $service)
    {
        ini_set('memory_limit', '-1');

        $this->info("Fetching products...");
        $items = $service->getProducts();
        $total = count($items);

        if ($total === 0) {
            $this->warn("No products found.");
            return;
        }

        $this->info("Total: {$total}");

        $chunks = array_chunk($items, 1000);

        foreach ($chunks as $chunk) {

            $clean = collect($chunk)->map(function ($p) {

                // safe value caster
                $safe = fn($v) =>
                is_array($v) || is_object($v)
                    ? json_encode($v, JSON_UNESCAPED_UNICODE)
                    : ($v === "" ? null : $v);

                return [
                    'source' => $safe($p['source']),
                    'vendor_id' => $safe($p['id']),

                    'title' => $safe($p['title']),
                    'slug' => Str::slug($p['title'] ?? ''),

                    'brand_name' => $safe($p['brand_name']),
                    'category' => $safe($p['category']),

                    'price' => $safe($p['price']),
                    'old_price' => $safe($p['old_price'] ?? null),
                    'stock' => $safe($p['stock'] ?? null),

                    'image' => $safe($p['image'] ?? null),
                    'images' => json_encode($p['images'] ?? [], JSON_UNESCAPED_UNICODE),

                    'label' => $safe($p['label'] ?? null),
                    'short_description' => $safe($p['short_description'] ?? null),
                    'description_html' => $safe($p['description_html'] ?? null),
                    'supplement_facts_html' => $safe($p['supplement_facts_html'] ?? null),

                    'weight' => $safe($p['weight'] ?? null),

                    'category_slug' => Str::slug($p['category'] ?? ''),
                    'brand_slug' => Str::slug($p['brand_name'] ?? ''),

                    'sku' => $safe($p['sku'] ?? null),
                    'upc' => $safe($p['upc'] ?? null),
                    'ean' => $safe($p['ean'] ?? null),
                ];
            })->toArray();

            DB::table('products')->upsert(
                $clean,
                ['source', 'vendor_id'],
                [
                    'title',
                    'slug',
                    'brand_name',
                    'category',
                    'price',
                    'old_price',
                    'stock',
                    'image',
                    'images',
                    'label',
                    'short_description',
                    'description_html',
                    'supplement_facts_html',
                    'weight',
                    'category_slug',
                    'brand_slug',
                    'sku',
                    'upc',
                    'ean'
                ]
            );
        }

        $this->info("✅ Sync finished");
        $this->info("📦 Total in DB: " . Product::count());
    }
}
