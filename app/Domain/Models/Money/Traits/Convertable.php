<?php

namespace App\Domain\Models\Money\Traits;

use App\Exceptions\NonReportable\BadCurrencyException;
use App\Exceptions\TException;
use App\Transaction;

trait Convertable
{
    public function convert(string $targetCur): static
    {
        if (! in_array($targetCur, Transaction::CUR)) {
            throw new BadCurrencyException($targetCur);
        }
        $this->amount = $this
          ->curService
          ->convert($this->currency, $targetCur, $this->amount);
        $this->currency = $targetCur;

        return $this;
    }
}
