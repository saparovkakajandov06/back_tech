<?php

namespace App\Domain\Services\Socgress;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;

class SocgressFake extends ASocgress
{
    public const CHARGE = 333.33;

    protected $count;

    public function add(Chunk $chunk, $params, $config): AddResult
    {
        $this->count = self::getLocalCountWithMods($chunk->details['count'], $config);
        $id = rand(0, 999999);

        return new AddResult(
            request: array_merge([
                'token' => 'hidden',
                'link' => $chunk->details['link'],
                'count' => $this->count,
            ], $config['remote_params']), // service_id, network, speed
            response: [
                "id" => $id,
                "status" => "activating",
                "title" => "Want a free Eggroll?...",
                "photo" => "https://scontent-amt2-1.cdninstagram.com/v/t51.2885-19/s150x150/56340225_585840631933636_6745398090015965184_n.jpg?_nc_ht=scontent-amt2-1.cdninstagram.com&_nc_ohc=Qs6wi5xwAZ8AX9MAJ6A&oh=18b3121b2ada72e046cb2dc192004c53&oe=5E8CCA8D",
                "link" => "https://www.instagram.com/p/ByFwgy6A-6w",
                "network" => "instagram",
                "type" => "like",
                "money_spent" => 3,
                "count" => 10,
                "start_done_count" => 10,
                "done_count" => 0,
                "created_at" => 1582422791,
            ],
            externId: $id,
            status: Order::STATUS_RUNNING,
            charge: $this->netCost($id, $this->count, $config),
        );
    }

    public function charge(int $orderId, int $count, $svcConfig): float
    {
        return self::CHARGE;
    }

    public function getStatus($orderId): ExternStatus
    {
        return new ExternStatus(
            status: Order::STATUS_COMPLETED,
            remains: $this->count,
            response: [
                "count" => 1,
                "items" => [
                    [
                        "id" => $orderId,
                        "status" => "done",
                        "title" => "Want a free Eggroll?...",
                        "photo" => "https://scontent-amt2-1.cdninstagram.com/v/t51.2885-19/s150x150/56340225_585840631933636_6745398090015965184_n.jpg?_nc_ht=scontent-amt2-1.cdninstagram.com&_nc_ohc=Qs6wi5xwAZ8AX9MAJ6A&oh=18b3121b2ada72e046cb2dc192004c53&oe=5E8CCA8D",
                        "link" => "https://www.instagram.com/p/ByFwgy6A-6w",
                        "network" => "instagram",
                        "type" => "like",
                        "money_spent" => 3,
                        "count" => 10,
                        "start_done_count" => 10,
                        "done_count" => 11,
                        "created_at" => 1582422791,
                    ],
                ],
            ]);
    }
}
