<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class VKTransport
{
    private $httpClient;
    private $access_token = "94076e9e79efe77233124eec24cd5a7ae8436604df155e77dea5130a40f213c9b7f7a6e395a992f60b606";
    private $v = "5.52";

    private $access_time;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->access_time = self::getMs();
    }

    public static function getMs()
    {
        return microtime(true) * 1000; // ms
    }

    public function sleepMs($ms)
    {
        usleep($ms * 1000);
    }

    public function throttle()
    {
        $WAIT_TIME = 340;

        $current_time = self::getMs();
        $delta = $current_time - $this->access_time;
        if ($delta < $WAIT_TIME) {
            $time_to_sleep = $WAIT_TIME - $delta;
            echo "time to sleep $time_to_sleep\n";
            $this->sleepMs($time_to_sleep);
        }
        $this->access_time = self::getMs();
    }

    public function get($methodName, $params)
    {
        $this->throttle();

        $params['access_token'] = $this->access_token;
        $params['v'] = $this->v;

        try {
            $vk_response = $this->httpClient->get('https://api.vk.com/method/' . $methodName,
                    ['query' => $params])->getBody()->getContents();
        } catch (GuzzleException $ex) {}

        return json_decode($vk_response);
    }
}
