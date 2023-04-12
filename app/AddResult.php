<?php

namespace App;

class AddResult
{
    public function __construct(
        public ?array $request = [],
        public ?array $response = [],
        public ?string $externId = null,
        public string $status = Order::STATUS_UNKNOWN,
        public float $charge = 0.0,
    )
    {
//        assert(in_array($status, [
//            Order::STATUS_RUNNING,
//            Order::STATUS_ERROR,
//        ]),
//        "AddResult: status <$status> is not available");
    }
}
