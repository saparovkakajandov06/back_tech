<?php

namespace App\Services\Currency;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CbrRateService
{
    protected string $url = 'https://www.cbr-xml-daily.ru/daily_json.js';
    protected int $ttl = 60 * 60 * 24;
    protected string $cacheKey = 'cbr_currency_rates';

    public function getRate(string $currency): float
    {
        $data = Cache::remember(
            $this->cacheKey,
            $this->ttl,
            $this->createFetcher()
        );

        $value = Arr::get($data, 'Valute.' . Str::upper($currency) . '.Value');
        $nominal = Arr::get($data, 'Valute.' . Str::upper($currency) . '.Nominal');

        return floatval($value) / floatval($nominal);
    }

    protected function createFetcher(): callable
    {
        return function() {
            return Http::get($this->url)->json();
        };
    }
}
