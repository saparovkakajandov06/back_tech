<?php

namespace App\Domain\Models\Money;

use App\Exceptions\NonReportable\BadCurrencyException;
use App\Services\CurrencyService;
use App\Services\Money\Services\TransactionsService;
use App\Transaction;

abstract class BaseMoney implements Money
{

    protected float $amount;

    protected string $currency;

    protected ?string $comment;

    protected int $precision = 4;

    protected int $mode = PHP_ROUND_HALF_EVEN;

    protected array $orderIds;

    protected TransactionsService $moneyService;
    protected CurrencyService $curService;

    public function __construct($amount, $currency, $comment = null, $orderIds = [])
    {
        if (! in_array($currency, Transaction::CUR)) {
            throw new BadCurrencyException($currency);
        }
        $this->amount   = $amount;
        $this->currency = $currency;
        $this->comment  = $comment;
        $this->moneyService = resolve(TransactionsService::class);
        $this->curService = resolve(CurrencyService::class);
        $this->orderIds = $orderIds;
    }

    public function val(): float
    {
        return round($this->amount, $this->precision, $this->mode);
    }

    public function __toString(): string
    {
        $s = $this->amount . " " .
             $this->currency . " " .
             $this->comment;
        return $s;
    }
}
