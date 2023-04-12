<?php

namespace App;

use Illuminate\Support\Facades\Cache;

class Cached
{
    protected $instance;
    protected $timeToLive;

    public function __construct($instance)
    {
        $this->instance = $instance;
        $this->timeToLive = 10; // seconds
    }

    public function __call($method, $args)
    {
        $key = "_" . $method . '@' . collect($args)->join('_');

        $value = Cache::remember($key, $this->timeToLive,
            function () use ($method, $args) {
                return call_user_func_array([$this->instance, $method], $args);
            });

        return $value;
    }
}
