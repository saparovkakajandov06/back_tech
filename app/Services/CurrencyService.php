<?php

namespace App\Services;

use App\Currency;
use App\Exceptions\NonReportable\BadCurrencyException;
use App\Services\Currency\CbrRateService;
use App\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CurrencyService
{
    protected CbrRateService $rateService;

    protected int $ttl = 60 * 60;

    public function __construct(CbrRateService $rateService)
    {
        $this->rateService = $rateService;
    }

    protected function buildCacheKey(string $currency): string
    {
        return 'currency_rate_' . Str::lower($currency);
    }

    protected function createFetcher(string $currency): callable
    {
        return function () use ($currency) {
            return Currency::where('sid', $currency)->firstOrFail()->rate;
        };
    }

    public function getRate(string $currency): float
    {
        if ($currency === Transaction::CUR_RUB) {
            return 1;
        }

        return Cache::remember(
            $this->buildCacheKey($currency),
            $this->ttl,
            $this->createFetcher($currency)
        );
    }

    public function convert(string $from, string $to, float $amount): float
    {
        if (!in_array($from, Transaction::CUR) || !in_array($to, Transaction::CUR)) {
            throw new BadCurrencyException($from);
        }

        if ($from === $to) {
            return $amount;
        }

        return $amount * $this->getRate($from) / $this->getRate($to);
    }

    public function updateRates(): void
    {
        foreach (Transaction::CUR as $currency) {
            $model = Currency::where('sid', $currency)->first();

            if (!$model) {
                $model = new Currency();
                $model->sid = $currency;
            }

            $model->rate = $currency === Transaction::CUR_RUB ? 1 : $this->rateService->getRate($currency);
            $model->saveOrFail();
        }
    }

    public function getRates(): array
    {
        $rates = [];

        foreach (Transaction::CUR as $currency) {
            $rates[$currency] = $this->getRate($currency);
        }

        return $rates;
    }
}
