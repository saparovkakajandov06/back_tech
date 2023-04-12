<?php

namespace App\Domain\Services\Socgress;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Socgress extends ASocgress
{
    const createUrl = 'https://api.socgress.com/1/createTask';
    const statusUrl = 'https://api.socgress.com/1/getTasks';
    const servicesUrl = 'https://api.socgress.com/1/getServices';

    protected string $token;

    public function __construct()
    {
        $this->token = env('SOCGRESS_TOKEN');
    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        $count = self::getLocalCountWithMods($chunk->details['count'], $svcConfig);

        $request = array_merge([
            'token' => $this->token,
            'link' => $chunk->details['link'],
            'count' => $count,
        ], $svcConfig['remote_params']); // service_id, network, speed, comment_type

        // comment_type: own, positive
        if (! empty($orderParams['comments'])) {
            $request['comments'] = $orderParams['comments'];
        }

        $response = null;
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->post(self::createUrl, $request)
                ->throw()
                ->json();
        }
        catch (\Throwable $e) {
            Log::info(__METHOD__ . ': Bad response from provider');
            Log::info(describe_exception($e));
        }

        $request['token'] = 'hidden';
        $res = new AddResult(request: $request, response: $response);

        if (isset($response['id'])) {
            $res->status = Order::STATUS_RUNNING;
            $res->externId = $response['id'];
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
                ->get(
                    self::statusUrl, [
                    'token' => $this->token,
                    'id'    => $orderId,
                ])
                ->throw()
                ->json();

            return $response['money_spent'] ?? 0.;
        } catch (Exception $e) {
            Log::info("[CHUNK_CHARGE] Socgress could not get charge on order {$orderId}");
            Log::info("[CHUNK_CHARGE] {$e->getMessage()}");
            Log::info("[CHUNK_CHARGE] {$e->getFile()}:{$e->getLine()}");
        }
        return 0.;
    }

    public function getStatus($orderId): ExternStatus
    {
        // queued, activating, active, done, stopped, deleted

        $res = Http::retry(3, 1000)
            ->timeout(10)
            ->post(self::statusUrl, [
                'token' => $this->token,
                'ids'   => $orderId,
            ])
            ->throw()
            ->json();

        try {
            $item = $res['items'][0];
        } catch (Exception $e) {
            return new ExternStatus(
                status: Order::STATUS_ERROR, remains: -1, response: $res);
        }

        $st = match ($item['status']) {
            'queued' => Order::STATUS_RUNNING,
            'done' => $item['done_count'] >= $item['count']
                ? Order::STATUS_COMPLETED
                : Order::STATUS_UNKNOWN,
            'active' => $item['done_count'] < $item['count']
                ? Order::STATUS_RUNNING
                : Order::STATUS_UNKNOWN,
            'stopped' => Order::STATUS_RUNNING,
            'deleted' => Order::STATUS_ERROR,
            default => Order::STATUS_ERROR,
        };

        return new ExternStatus(
            status: $st,
            remains: $item['count'] - $item['done_count'],
            response: $res,
        );
    }
}
