<?php

namespace App\Domain\Services\NakrutkaAuto;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NakrutkaAuto extends ANakrutkaAuto
{
    private $url;
    private $key;

    public function __construct()
    {
        $this->url = env('NAKRUTKA_API_URL');
        $this->key = env('NAKRUTKA_API_KEY');
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

        // In progress - выполняется
        // Pending - ожидает
        // Processing - обрабатывается

        // Partial - частично выполнен. возврат.
        // Canceled - отменен
        // Completed - выполнен
        $remoteStatus = $res['status'];

        if (empty($remoteStatus)) {
            return new ExternStatus(
                status: Order::STATUS_ERROR,
                completed: 0,
                response: $res
            );
        }
        if ($remoteStatus === 'Pending') {
            return new ExternStatus(
                status: Order::STATUS_RUNNING,
                completed: 0,
                response: $res
            );
        }

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
            default => Order::STATUS_UNKNOWN,
        };

        // у автоуслуг нет единого $res['remains'] для всего заказа, только индивидуальные у каждого подзаказа
        $remains = 0;
        if (! empty($res['orders'])) {
            $subOrders = implode(',', $res['orders']);

            $res = Http::retry(3, 1000)
                ->timeout(10)
                ->get($this->url, [
                    'key'    => $this->key,
                    'action' => 'status',
                    'orders' => $subOrders,
                ])
                ->throw()
                ->json();

            echo "ids: $subOrders";
            echo "received from ANakrutkaAuto " . json_encode($res) . PHP_EOL;

            try {
                $order = Chunk::where('extern_id', $orderId)
                    ->where('service_class', ANakrutkaAuto::class)
                    ->firstOrFail()
                    ->compositeOrder;
                $perPost = avg($order->params['min'], $order->params['max']);
                foreach ($res as $id => $data) {
                    $remains += intval($data['remains'] ?? $perPost);
                }
            } catch (\Throwable $e) {
                Log::stack(['daily', 'orders'])->error($e->getMessage());
            }
        }

        return new ExternStatus(
            status: $status,
            remains: $remains,
            response: $res
        );
    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        $min = self::getLocalCountWithMods($orderParams['min'], $svcConfig);
        $max = self::getLocalCountWithMods($orderParams['max'], $svcConfig);
        $count = $orderParams['posts'] * avg($min, $max);

        $request = array_merge([
            'key' => $this->key,
            'action' => 'add',
            'username' => $orderParams['login'],
            'min' => $min,
            'max' => $max,
            'posts' => $orderParams['posts'],
        ], $svcConfig['remote_params']); // service, delay

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
        } else {
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
        } catch (Exception $e) {
            Log::info("[CHUNK_CHARGE] AutoNakrutka could not get charge on order {$orderId}");
            Log::info("[CHUNK_CHARGE] {$e->getMessage()}");
            Log::info("[CHUNK_CHARGE] {$e->getFile()}:{$e->getLine()}");
        }
        return 0.;
    }
}
