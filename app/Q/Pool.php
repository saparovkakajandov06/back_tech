<?php

namespace App\Q;

use Illuminate\Support\Collection;

class Pool
{
    public Collection $slaves;
    protected int $index;

    public function __construct()
    {
        $this->slaves = new Collection();
        $this->index = 0;
    }

    public function addSlave(Slave $slave) : void
    {
        $this->slaves->push($slave);
    }

    public function addSlaves(array $ids) : void
    {
        foreach($ids as $id) {
            $this->slaves->push(new Slave($id));
        }
    }

    public function nextSlave() : Slave
    {
        $slave = $this->slaves[$this->index];
        $this->index++;
        if($this->index == $this->slaves->count()) {
            $this->index = 0;
        }
        return $slave;
    }

    public function each(callable $callback)
    {
        $this->slaves->each($callback);
    }
}
