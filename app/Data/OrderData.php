<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use App\Data\OrderItemData;

class OrderData extends Data
{
    public function __construct(
        public int $client_id,
        /** @var OrderItemData[] */
        public array $items,
    ) {}
}
