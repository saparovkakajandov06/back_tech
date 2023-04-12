<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class CurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Echo db currencies rates';

    public function handle(CurrencyService $currencyService)
    {
        dd($currencyService->getRates());
    }
}
