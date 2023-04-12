<?php

namespace App\Domain\Services\Everve;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use App\Services\CurrencyService;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Everve extends AEverve
{
    const url = 'https://api.everve.net/v2/orders';

    protected string $token;

    public function __construct()
    {
        $this->token = env('EVERVE_TOKEN');
    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        $count = self::getLocalCountWithMods($chunk->details['count'], $svcConfig);

        $request = array_merge([
            'api_key' => $this->token,
            'order_url' => $orderParams['link'],
            'order_overall_limit' => $count,
            'clear_prev_stat' => 1,
        ], $svcConfig['remote_params']); // category_id, order_price

        if (! empty($svcConfig['order_price'])) {
            $request['order_price'] = $svcConfig['order_price'];
        }

        $response = null;
        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->post(self::url . '?' . Arr::query($request))
                ->throw();
            // Log::channel('suppliers')->info(__METHOD__ . $response->body());
            $response = $response->json();
        }
        catch (\Throwable $e) {
            Log::info(__METHOD__ . ": Bad response from provider");
            Log::info(describe_exception($e));
        }

        $request['api_key'] = 'hidden';
        $res = new AddResult(request: $request, response: $response);

        if (!empty($response['order_id'])) {
            $res->status = Order::STATUS_RUNNING;
            $res->externId = $response['order_id'];
            $res->charge = $this->netCost($res->externId, $count, $svcConfig);
        }
        else {
            $res->status = Order::STATUS_ERROR;
        }
        return $res;
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        $currencyService = resolve(CurrencyService::class);
        $USDRate = $currencyService->getRate('USD');

        try {
            $response = Http::retry(3, 1000)
                ->timeout(10)
                ->get(self::url . '/' . $orderId, [
                    'api_key' => $this->token,
                ])
                ->throw();
            // Log::channel('suppliers')->info(__METHOD__ . $response->body());
            $response = $response->json();
            $usdPer1 = $response['order_price'] ?? 0.;
            return $count * $usdPer1 * $USDRate;
        } catch (Exception $e) {
            Log::info('[CHUNK_CHARGE] ' . __CLASS__ . " could not get charge on order {$orderId}");
            Log::info('[CHUNK_CHARGE] ' . describe_exception($e));
        }
        return 0.;
    }

    public function getStatus($orderId): ExternStatus
    {
        $response = Http::retry(3, 1000)
            ->timeout(10)
            ->get(self::url . '/' . $orderId, [
                'api_key' => $this->token,
            ])
            ->throw();
        // Log::channel('suppliers')->info(__METHOD__ . $response->body());
        $response = $response->json();

        // empty response - error
        if (! $response) {
            return new ExternStatus(
                externId: $orderId,
                response: [],
                status: Order::STATUS_ERROR
            );
        }

        $es = new ExternStatus(externId: $orderId, response: $response);

        if(empty($response['order_status'])) {
            if($response['error_message'] === "Wrong order ID or order does not exist"){
                $es->status = Order::STATUS_ERROR;
            } else {
                $es->status = Order::STATUS_RUNNING;
            }
            return $es;
        }

        switch($response['order_status']) {
            case 'in_progress':
                $es->status = Order::STATUS_RUNNING;
                $es->completed = $response['order_overall_counter'];
                break;
            case 'completed':
                $es->status = Order::STATUS_COMPLETED;
                $es->remains = 0;
                break;
            case 'blocked':
                $es->status = Order::STATUS_RUNNING;
                $es->completed = $response['order_overall_counter'];
                break;
            case 'deleted':
                $es->status = Order::STATUS_ERROR;
                $es->completed = $response['order_overall_counter'];
        }

        return $es;
    }
}
