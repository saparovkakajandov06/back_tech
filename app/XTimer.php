<?php

namespace App;

class XTimer
{
    protected array $timers;
    protected array $deltas;

    public function __construct()
    {
        $this->timers = [];
        $this->deltas = [];
    }

    public function start($id='0')
    {
        $this->timers[$id] = microtime(true); // float seconds
    }

    public function stop($id='0')
    {
        $this->deltas[$id] = microtime(true) - $this->timers[$id];
    }
    public function get($id='0'): float
    {
        return $this->deltas[$id];
    }
}
