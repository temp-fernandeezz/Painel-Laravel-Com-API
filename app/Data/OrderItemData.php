<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class OrderItemData extends Data
{
    public function __construct(
        public int $product_id,
        public int $quantity,
        public float $unit_price,
    ) {}
}