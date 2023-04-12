<?php

namespace App\Console\Commands;

use App\PaymentMethod;
use Illuminate\Console\Command;

class PspSeedDisableConnectum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psp-seed:disable-connectum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable all connectum payment methods';

    public function handle()
    {
        PaymentMethod::where('payment_system', 'connectum')
            ->update([
                'active_flag' => false
            ]);
    }
}
