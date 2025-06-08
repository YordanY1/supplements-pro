<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'BGN', 'symbol' => 'лв.', 'name' => 'Bulgarian Lev'],
            ['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro'],
            ['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar'],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['code' => $currency['code']],
                ['symbol' => $currency['symbol'], 'name' => $currency['name']]
            );
        }
    }
}
