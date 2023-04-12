<?php

namespace App\Services;

use App\Exceptions\Reportable\DistributorException;
use App\Exceptions\TException;
use Exception;

class Distributor
{
    private $ss;

    public function __construct(array $services)
    {
        $this->ss = $services;
        $this->cs = collect($services);
    }

    private function sortAOSByOrder()
    {
        usort($this->ss, fn($a, $b) => $a['order'] - $b['order']);
    }

    private function getMin()
    {
        return $this->cs->min('min');
    }

    private function getTotalMax()
    {
        return $this->cs->sum('max');
    }

    public function getDistribution(int $n): array
    {
        if ($n < $this->getMin()) {
            throw new DistributorException(__('s.value_too_small', [
                'value' => $n,
            ]));
        }
        if ($n > $this->getTotalMax()) {
            throw new DistributorException(__('s.value_too_large', [
                'value' => $n,
            ]));
        }

        $this->sortAOSByOrder();

        $todo = $n;

        for ($i = 0; $i < count($this->ss); $i++) {
            $cur = &$this->ss[$i];

            if ($todo >= $cur['max']) { // все ок, укладываем в максимум
                $n = $cur['max'];
            } elseif ($todo >= $cur['min']) { // все ок, уложим между мин и макс
                $n = $todo;
            } else {
                for ($j = $i - 1; $j >= 0; $j--) {
                    $prev = &$this->ss[$j];
                    while ($prev['n'] > $prev['min']) {
                        $prev['n']--;
                        $todo++;
                        if ($todo >= $cur['min']) {
                            $n = $todo;
                            goto done;
                        }
                    }
                    $x = $todo + $prev['min'];
                    if ( $x >= $cur['min'] && $x <= $cur['max'] ) {
                        // можем уменьшить prev['n'] до нуля
                        $prev['n'] = 0;
                        $n = $x;
                        goto done;
                    }
                }
                throw new DistributorException();
            }
            done:
            $cur['n'] = $n;
            $todo -= $n;
            if ($todo <= 0) {
                break;
            }
        }

        $res = [];
        foreach ($this->ss as $s) {
            $res[$s['name']] = $s['n'];
        }
        return $res;
    }
}