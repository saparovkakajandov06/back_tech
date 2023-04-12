<?php

namespace App\Domain\Services\Prm4u;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Prm4u extends APrm4u
{
    private $url;
    private $key;

    public function __construct()
    {
        $this->url = env('PRM4U_API_URL');
        $this->key = env('PRM4U_API_KEY');
    }

    private function statusFromData(?array $remoteResponse): ExternStatus
    {
        if (is_null($remoteResponse)) {
            return new ExternStatus(status: Order::STATUS_ERROR);
        }

        [
            'status' => $remoteStatus,
            'remains' => $remains,
        ] = $remoteResponse;

        // In progress - выполняется
        // Pending - ожидает
        // Processing - обрабатывается

        // Partial - частично выполнен. возврат.
        // Canceled - отменен
        // Completed - выполнен

        $status = match ($remoteStatus) {
            'Processing',
            'Pending',
            'Active',
            'In progress' => Order::STATUS_RUNNING,
            'Completed' => Order::STATUS_COMPLETED,
            'Partial' => Order::STATUS_PARTIAL_COMPLETED,
            'Paused' => Order::STATUS_PAUSED,
            'Canceled' => Order::STATUS_CANCELED,
            'Deleted' => Order::STATUS_ERROR,
            default => Order::STATUS_ERROR,
        };

        if ($status === 'Pending') {
            return new ExternStatus(
                status: $status,
                completed: 0,
                response: $remoteResponse
            );
        }
        else {
            return new ExternStatus(
                status: $status,
                remains: $remains,
                response: $remoteResponse
            );
        }
    }

    public function getStatus($orderId): ExternStatus
    {
        $res = Http::retry(3, 1000)
            ->timeout(10)
            ->get($this->url, [
                'key'    => $this->key,
                'action' => 'status',
                'order'  => $orderId,
            ])
            ->throw()
            ->json();
        $exStatus = $this->statusFromData($res);
        $exStatus->externId = $orderId;

        return $exStatus;
    }

    public function getManyStatuses(array $ids): array
    {
        echo "called Prm4u: getManyStatuses with params:" . PHP_EOL . json_encode($ids) . PHP_EOL;

        $ids = implode(',', $ids);

        $res = Http::retry(3, 1000)
            ->timeout(10)
            ->get($this->url, [
                'key'    => $this->key,
                'action' => 'status',
                'orders' => $ids,
            ])
            ->throw()
            ->json();

        echo "received from Prm4u " . json_encode($res) . PHP_EOL;
        $ess = [];
        foreach ($res as $id => $data) {
            $externStatus = $this->statusFromData($data);
            $externStatus->externId = $id;
            $ess[] = $externStatus;
        }

        return $ess;
    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        $count = self::getLocalCountWithMods($chunk->details['count'], $svcConfig);

        $request = array_merge([
            'key' => $this->key,
            'action' => 'add',
            'link' => $chunk->details['link'],
            'quantity' => $count,
        ], $svcConfig['remote_params']);

        $response = null;
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->get($this->url, $request)
                ->throw()
                ->json();
        }
        catch (\Throwable $e) {
            Log::info(__METHOD__ . ": Bad response from provider");
            Log::info(describe_exception($e));
        }

        $request['key'] = 'hidden';
        $res = new AddResult(request: $request, response: $response);

        if (isset($response['order'])) {
            $res->status = Order::STATUS_RUNNING;
            $res->externId = $response['order'];
            $res->charge = $this->netCost($res->externId, $count, $svcConfig);
        }
        else {
            $res->status = Order::STATUS_ERROR;
        }
        return $res;
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->get($this->url, [
                    'key'    => $this->key,
                    'action' => 'status',
                    'order'  => $orderId,
                ])
                ->throw()
                ->json();

            return $response['charge'] ?? 0.;
        } catch (\Exception $e) {
            Log::info("[CHUNK_CHARGE] Prm4u could not get charge on order {$orderId}");
            Log::info("[CHUNK_CHARGE] {$e->getMessage()}");
            Log::info("[CHUNK_CHARGE] {$e->getFile()}:{$e->getLine()}");
        }
        return 0.;
    }
}
