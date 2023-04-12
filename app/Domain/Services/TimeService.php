<?php

namespace App\Domain\Services;

use App\AddResult;
use App\Domain\Models\Chunk;
use App\ExternStatus;
use App\Order;
use Illuminate\Support\Carbon;
use Exception;
use GuzzleHttp\Client;

class TimeService extends ATime
{
    public Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public function add(Chunk $chunk, $params, $config): AddResult
    {
        // add to extern service
        // do nothing
        return new AddResult(status: Order::STATUS_RUNNING, externId: 111);
    }

    public function charge($orderId, $count, $svcConfig): float
    {
        return 0.;
    }

    public function getStatus($id): ExternStatus
    {
        //$url = 'http://worldclockapi.com/api/json/utc/now';
        $url = 'http://worldtimeapi.org/api/timezone/europe/moscow';
        $response = $this->httpClient->request('GET', $url);

        $responseObj = json_decode($response->getBody()->getContents());
        try {
            $c = new Carbon($responseObj->utc_datetime);
            $time = $c->setTimezone('Europe/Moscow')->toDateTimeString();
//            $time = $c->toDateTimeString();
        } catch (Exception $e) {
        }

        return new ExternStatus(
            status: Order::STATUS_RUNNING,
            remains: 0,
            response: [$time],
        );
    }
}
