<?php

namespace App\Console\Commands;

use App\Domain\Services\Fake\AFake;
use App\Domain\Splitters\DefaultSplitter;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckUserHasEnoughFunds;
use App\Transaction;
use App\User;
use App\UserService;
use App\USPrice;
use Illuminate\Console\Command;

class GiveMoneyToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'give_money_to_admin';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $admin = User::where('email', 'admin@smm.example.com')->first();
        if (! $admin) {
            echo "admin not found";
            return 1;
        }

        $admin->giveMoney(1000.0, Transaction::CUR_RUB);
        $admin->giveMoney(50.0, Transaction::CUR_USD);

        echo $admin->getBalance();
        echo "\n";

        return 0;
    }
}
