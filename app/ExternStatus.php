<?php


namespace App;

/**
 * Статус заказа во внешнем сервисе
 */
class ExternStatus
{
    public function __construct(
        public ?string $externId = null,
        public string  $status = Order::STATUS_UNKNOWN,
        public ?int $completed = null,
        public ?int $remains = null,
        public ?array $response = []
    )
    {
//        assert(in_array($status, [
//            Order::STATUS_RUNNING,
//            Order::STATUS_ERROR,
//            Order::STATUS_CANCELED,
//        ]),
//        "ExternStatus: status <$status> is not available");
    }

    public function __toString(): string
    {
        return json_encode([
            'status' => $this->status,
            'remains' => $this->remains,
            'response' => $this->response,
        ], JSON_PRETTY_PRINT);
    }

    public function d()
    {
        return "$this->externId : $this->status : $this->remains";
    }

    public function p()
    {
        return print_r($this, true);
    }
}
