<?php

namespace App\Domain\Services\Nakrutka;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Nakrutka extends ANakrutka
{
    private $url;
    private $key;

//    public function order($data)
//    { // add order
//        $post = array_merge(array('key' => $this->api_key, 'action' => 'add'),
//            $data);
//
//        return json_decode($this->connect($post));
//    }

    public function __construct()
    {
        $this->url = env('NAKRUTKA_API_URL');
        $this->key = env('NAKRUTKA_API_KEY');
    }

    private function statusFromData(?array $remoteResponse): ExternStatus
    {
        if (is_null($remoteResponse)) {
            // throw new ReportableException("Remote response is null");
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
        if ($remoteStatus === 'Pending') {
            return new ExternStatus(
                status: Order::STATUS_RUNNING,
                completed: 0,
                response: $remoteResponse
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
            default => Order::STATUS_ERROR,
        };
        return new ExternStatus(
            status: $status,
            remains: $remains,
            response: $remoteResponse
        );
    }

//{
//"charge":"0.37",
//"start_count":"89",
//"status":"Completed",
//"remains":"0",
//"currency":"RUB"
//}
    // get order status
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
        echo "called Nakrutka: getManyStatuses with params:\n";
        echo json_encode($ids);
        echo "\n";

        $ids = collect($ids)->join(',');

//        $res = Http::asForm()->post($this->nakrutka->url, [
//            'key' => $this->nakrutka->key,
//            'action' => 'status',
//            'orders' => $ids,
//        ])->json();

        $res = Http::retry(3, 1000)
            ->timeout(10)
            ->get($this->url, [
                'key'    => $this->key,
                'action' => 'status',
                'orders' => $ids,
            ])
            ->throw()
            ->json();

        echo "received from nakrutka " . json_encode($res) . "\n";
        $ess = [];
        foreach ($res as $id => $data) {
            $externStatus = $this->statusFromData($data);
            $externStatus->externId = $id;
            $ess[] = $externStatus;
        }

//        echo "extern statuses size " . count($ess) . "\n";
        return $ess;
    }

//    public function multiStatus($order_ids)
//    { // get order status
//        return json_decode($this->connect(array(
//            'key' => $this->api_key,
//            'action' => 'status',
//            'orders' => implode(",", (array)$order_ids),
//        )));
//    }
//
//    public function services()
//    { // get services
//        return json_decode($this->connect(array(
//            'key' => $this->api_key,
//            'action' => 'services',
//        )));
//    }
//
//    public function balance()
//    { // get balance
//        return json_decode($this->connect(array(
//            'key' => $this->api_key,
//            'action' => 'balance',
//        )));
//    }


//    private function connect($post)
//    {
//        $_post = Array();
//        if (is_array($post)) {
//            foreach ($post as $name => $value) {
//                $_post[] = $name.'='.urlencode($value);
//            }
//        }
//
//        $ch = curl_init($this->api_url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//        if (is_array($post)) {
//            curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
//        }
//        curl_setopt($ch, CURLOPT_USERAGENT,
//            'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
//        $result = curl_exec($ch);
//        if (curl_errno($ch) != 0 && empty($result)) {
//            $result = false;
//        }
//        curl_close($ch);
//
//        return $result;
//    }

    public function add(Chunk $chunk, $orderParams, $svcConfig): AddResult {
        // для обычных услуг
        // we got check below in the pipeline so it goes away
        // if (empty($chunk->details['link']) || empty($chunk->details['count']))
        //     throw new ReportableException('Nakrutka add() needs link and count');
        $count = self::getLocalCountWithMods($chunk->details['count'], $svcConfig);

        $request = array_merge([
            'key' => $this->key,
            'action' => 'add',
            'link' => $chunk->details['link'],
            'quantity' => $count,
        ], $svcConfig['remote_params']); // service

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
            Log::info("[CHUNK_CHARGE] Nakrutka could not get charge on order {$orderId}");
            Log::info("[CHUNK_CHARGE] {$e->getMessage()}");
            Log::info("[CHUNK_CHARGE] {$e->getFile()}:{$e->getLine()}");
        }
        return 0.;
    }
}
