<?php

namespace App\Data;

class ShippingData
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $phone,
        public string $city,
        public string $zip,
        public string $street,
        public string $payment_method,
        public float $weight
    ) {}
}
