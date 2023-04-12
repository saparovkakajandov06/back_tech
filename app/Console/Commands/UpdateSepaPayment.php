<?php

namespace App\Console\Commands;

use App\PaymentMethod;
use Illuminate\Console\Command;

class UpdateSepaPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sepa_langs:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency db rates from remote server rates';

    public function handle()
    {
        PaymentMethod::where('payment_system','monerchy')
            ->update([
                'titles' => [
                    'ru' => 'Онлайн банк',
                    'en' => 'Online bank',
                    'de' => 'Online-Bank',
                    'es' => 'Banco en línea',
                    'pt' => 'Banco online',
                    'it' => 'Banca in linea',
                    'tr' => 'Çevrimiçi banka',
                    'uk' => 'Онлайн банк'
                ],
            ]);
    }
}
