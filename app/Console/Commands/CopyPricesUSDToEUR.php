<?php

namespace App\Console\Commands;

use App\USPrice;
use Illuminate\Console\Command;

class CopyPricesUSDToEUR extends Command
{
    protected $signature = 'copy:prices_usd_to_eur';

    public function copyPrices()
    {
        $prices = USPrice::all();
        foreach($prices as $price) {
            $price->EUR = $price->USD;
            $price->save();
        }
    }

    public function handle()
    {
        $this->copyPrices();
        echo PHP_EOL . '--- done ---' . PHP_EOL;
    }
}
