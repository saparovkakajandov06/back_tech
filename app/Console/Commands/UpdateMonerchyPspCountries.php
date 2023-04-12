<?php

namespace App\Console\Commands;

use App\PaymentMethod;
use Illuminate\Console\Command;

class UpdateMonerchyPspCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psp:update_monerchy_countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update monerchy psp avaliable countries';

    public function handle()
    {
        PaymentMethod::where('payment_system', 'monerchy')
            ->update([
                'countries' => [
                    'AT',
                    'PT',
                    'BE',
                    'BG',
                    'ES',
                    'HR',
                    'CY',
                    'CZ',
                    'DK',
                    'EE',
                    'FI',
                    'FR',
                    'GF',
                    'DE',
                    'GI',
                    'GR',
                    'GP',
                    'GG',
                    'HU',
                    'IS',
                    'IE',
                    'IM',
                    'IT',
                    'JE',
                    'LV',
                    'LI',
                    'LT',
                    'LU',
                    'MT',
                    'MQ',
                    'YT',
                    'MC',
                    'NL',
                    'NO',
                    'PL',
                    'RE',
                    'RO',
                    'BL',
                    'MF',
                    'PM',
                    'SM',
                    'SK',
                    'SI',
                    'SE',
                    'CH',
                    'GB',
                    'US'
                ],
            ]);
    }
}
