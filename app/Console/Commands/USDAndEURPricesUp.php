<?php

namespace App\Console\Commands;

use App\USPrice;
use Illuminate\Console\Command;

class USDAndEURPricesUp extends Command
{
    protected $signature = 'config:usd_and_eur_prices_up';

    public function copyPrices()
    {
        $USSPrices = USPrice::all();
        foreach($USSPrices as $USPrices) {
            $currencies = ['USD', 'EUR'];
            foreach($currencies as $cur) {
                $prices = $USPrices->$cur;
                if ($prices[1] < 0.01) {
                    $prices[1] = 0.01;
                }
                $USPrices->$cur = $prices;
            }
            $USPrices->save();
        }
    }

    public function handle()
    {
        $this->copyPrices();
        echo PHP_EOL . '--- done ---' . PHP_EOL;
    }
}
