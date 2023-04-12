<?php

namespace App\Domain\Services\Vtope;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Vtope extends AVtope
{
    protected $user;
    protected $key;
    protected $token_rate;

    const apiUrl = 'https://vto.pe/api';

    public function __construct()
    {
        $this->user = env('VTOPE_USER');
        $this->key = env('VTOPE_KEY');
        $this->token_rate = env('VTOPE_RATE', 19.);
    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        $count = self::getLocalCountWithMods($chunk->details['count'], $svcConfig);
        $request = array_merge([
            'user' => $this->user,
            'key' => $this->key,
            'link' => $chunk->details['link'],
            'count' => $count,
        ], $svcConfig['remote_params']); // method, service, type

        $response = null;
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->post(self::apiUrl, $request)
                ->throw()
                ->json();
        }
        catch (\Throwable $e) {
            Log::info(__METHOD__ . ": Bad response from provider");
            Log::info(describe_exception($e));
        }

        $request['key'] = 'hidden';
        $res = new AddResult(request: $request, response: $response);

        if ($response['errorcode'] == 0) {
            $res->status = Order::STATUS_RUNNING;
            $res->externId = $response['id'];
            $res->charge = $this->netCost($res->externId, $count, $svcConfig);
        }
        else {
            $res->status = Order::STATUS_ERROR;
        }
        return $res;
    }

    public function charge(int $orderId, $count, $serviceConfig): float
    {
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->get(self::apiUrl, [
                    'user'   => $this->user,
                    'key'    => $this->key,
                    'method' => 'prices',
                ])
                ->throw()
                ->json();

            $response = Arr::get(
                $response,
                $serviceConfig['service'] . $serviceConfig['method'] //key
            );
            $per1tokens = $response ?? 0.;
            return $count * $per1tokens * $this->token_rate * 0.001;
        } catch (Exception $e) {
            Log::info("[CHUNK_CHARGE] Vtope could not get charge on order {$orderId}");
            Log::info("[CHUNK_CHARGE] {$e->getMessage()}");
            Log::info("[CHUNK_CHARGE] {$e->getFile()}:{$e->getLine()}");
        }
        return 0.;
    }

    public function getStatus($id): ExternStatus
    {
//        {
//            "count": 0, // оставшееся количество выполнений
//            "status": "ok",
//            "initcount": 1, // всего заказано выполнений
//            "errorcode": 0,
//            "starting_count": 16
//        }

        $response = Http::retry(3, 1000)
            ->timeout(10)
            ->post(self::apiUrl, [
                'user'   => $this->user,
                'key'    => $this->key,
                'method' => 'progress',
                'id'     => $id,
            ])
            ->throw()
            ->json();

        $status = Order::STATUS_UNKNOWN;

        if (isset($response['status'])) {
            if ($response['status'] === 'checking') {
                $status = Order::STATUS_RUNNING;
            } elseif ($response['status'] === 'ok' && $response['count'] === 0) {
                $status = Order::STATUS_COMPLETED;
            } elseif ($response['status'] === 'ok' && $response['count'] > 0) {
                $status = Order::STATUS_RUNNING;
            } elseif ($response['status'] !== 'ok') {
                $status = Order::STATUS_ERROR;
            }
        } elseif (isset($response['errorcode'])) {
            if ($response['errorcode'] == 101) { // not found
                $status = Order::STATUS_ERROR;
            }
        }

        return new ExternStatus(
            status: $status,
            remains: $response['count'] ?? null,
            response: $response,
        );
    }
}
