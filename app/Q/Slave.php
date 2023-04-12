<?php

namespace App\Q;

use Illuminate\Support\Facades\Redis;

class Slave
{
    const START_TIMER = 'start_timer';
    const GET_TIME = 'get_time';
    const DRAW_LINE = 'draw_line';
    const SAY = 'say';
    const PROCESS_ORDER = 'process_order';
    const EXIT_BY_TIME = 'exit_by_time';
    const EXIT = 'exit';

    public $id;
    protected $channel;

    public function __construct($id)
    {
        $this->id = $id;
        $this->channel = 'slave' . $id;
    }

    public function startTimer()
    {
        Redis::publish($this->channel, json_encode([
            'cmd' => self::START_TIMER,
        ]));
    }

    public function getTime()
    {
        Redis::publish($this->channel, json_encode([
            'cmd' => self::GET_TIME
        ]));
    }

    public function drawLine()
    {
        Redis::publish($this->channel, json_encode([
            'cmd' => self::DRAW_LINE
        ]));
    }

    public function say($msg)
    {
        Redis::publish($this->channel, json_encode([
            'cmd' => self::SAY,
            'payload' => $msg,
        ]));
    }

    public function processOrder($id)
    {
        Redis::publish($this->channel, json_encode([
            'cmd' => self::PROCESS_ORDER,
            'payload' => $id,
        ]));
    }

    public function exitByTime($seconds)
    {
        Redis::publish($this->channel, json_encode([
            'cmd' => self::EXIT_BY_TIME,
            'payload' => $seconds,
        ]));
    }

    public function exit()
    {
        Redis::publish($this->channel, json_encode([
            'cmd' => self::EXIT,
        ]));
    }
}
