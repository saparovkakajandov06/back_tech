<?php

namespace App\Services;

class ProfilerService
{
    protected $timers;
    protected $results;

    public function __construct()
    {
        $this->timers = [];
        $this->results = [];
    }

    public function start($name)
    {
        $this->timers[$name] = microtime(true);
    }

    public function stop($name)
    {
        $res = microtime(true) - $this->timers[$name];
        if (empty($this->results[$name])) {
            $this->results[$name] = $res;
        } else {
            $this->results[$name] += $res;
        }

        return $this->results[$name];
    }

    public function get($name)
    {
        if (! empty($this->results[$name])) {
            return $this->results[$name];
        }
        return 'not_set';
    }

    public function dump($base=null)
    {
        foreach ($this->results as $name => $value) {
            $s = $name . ": $value";
            if ($base) {
                $percent = $value / $this->results[$base];
                $s .= ' [' . $percent * 100 . '%]';
            }
            echo $s . "\n";
        }
    }
}
