<?php

namespace Tests\TF;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\AssertableJsonString;

trait DynamicDefinition {

    public function __call($name, $args) {
        if (is_callable($this->$name)) {
            return call_user_func($this->$name, $args);
        }
        else {
            throw new \RuntimeException("Method {$name} does not exist");
        }
    }

    public function __set($name, $value) {
        $this->$name = is_callable($value)?
            $value->bindTo($this, $this):
            $value;
    }
}

class TFHttp
{
    const E2E_ROOT = 'http://172.29.0.1:1313';

    private static function getFullUrl(string $url): string
    {
        // remove slash
        if ($url[0] == "/") {
            $url = Str::substr($url, 1);
        }
        return self::E2E_ROOT . '/' . $url;
    }

    public static function get(string $url, array $query = [], array $headers = [])
    {
        $url = self::getFullUrl($url);

        $res = empty($headers) ?
               Http::get($url, $query) :
               Http::withHeaders($headers)->get($url, $query);

        return new TFResponse($res);
    }

    public static function post(string $url, array $data = [], array $headers = [])
    {
        $url = self::getFullUrl($url);

        $res = empty($headers) ?
            Http::post($url, $data) :
            Http::withHeaders($headers)->post($url, $data);
        return new TFResponse($res);
    }
}
