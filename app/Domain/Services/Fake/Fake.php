<?php

namespace App\Domain\Services\Fake;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Fake extends AFake
{
    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        $targetName = $svcConfig['target'];
        $count = self::getLocalCountWithMods($chunk->details['count'], $svcConfig);

        $request = [
            'link' => $chunk->details['link'],
            'count' => $count,
            'target' => $orderParams[$targetName],
        ];

        $url = env('FAKE_SERVICE_URL') . '/api/orders';

        $response = null;
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->post($url, $request)
                ->throw()
                ->json();
        }
        catch (\Throwable $e) {
            Log::info(__METHOD__ . ": Bad response from provider");
            Log::info(describe_exception($e));
        }

        $res = new AddResult(request: $request, response: $response);

        if('success' !== $response['message'] || empty($response['order.id'])) {
            $res->status = Order::STATUS_ERROR;
        }
        else {
            $res->status = Order::STATUS_RUNNING;
            $res->externId = $response['order.id'];
            // is this correct? do we need it?
            $res->charge = $this->netCost($res->externId, $count, $svcConfig);
        }
        return $res;
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        return 0.;
    }

    public function getStatus(int $orderId): ExternStatus
    {
//        Log::info("Fake getStatus() id = $orderId");

        $url = env('FAKE_SERVICE_URL') . '/api/orders/' . $orderId;
        $response = Http::retry(3, 1000)
            ->timeout(10)
            ->get($url)
            ->throw();

        $newStatus = $response->json('order.status') ?? null;

        if (in_array($newStatus, [
            Order::STATUS_RUNNING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELED,
            Order::STATUS_PAUSED,
            Order::STATUS_ERROR,
        ])) {
            $status = $newStatus;
        } else {
            $status = Order::STATUS_ERROR;
        }

        $count = $response->json('order.count');
        $completed = $response->json('order.completed');

        return new ExternStatus(
            externId: $response->json('order.id'),
            status: $status,
            completed: $completed,
            remains: $count - $completed,
            response: $response->json());
    }
}
