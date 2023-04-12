<?php

namespace App\Console\Commands;

use App\PremiumStatus;
use App\Services\Money\Services\TransactionsService;
use App\Transaction;
use App\User;
use Illuminate\Console\Command;

class LoyaltiesFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:loyalties_fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix USD|EUR users premium statuses';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(TransactionsService $transactionsService)
    {
        $statuses = [
            Transaction::CUR_USD => PremiumStatus::where('cur', Transaction::CUR_USD)->orderBy('id')->get(),
            Transaction::CUR_EUR => PremiumStatus::where('cur', Transaction::CUR_EUR)->orderBy('id')->get(),
        ];
        $users = User::where('cur', '!=', Transaction::CUR_RUB)->where('cur', '!=', Transaction::CUR_UAH)->whereIn('premium_status_id', [1,2,3,4,5])->lazy();
        foreach ($users as $user) {
            $paymentsSum = $transactionsService->paymentsSum($user, $user->cur);
            $newStatus = $statuses[$user->cur][$user->parent_id ? 1 : 0];
            foreach ($statuses[$user->cur] as $status) {
                if (
                    $status->cash > $newStatus->cash &&
                    $status->id > $newStatus->id &&
                    $paymentsSum >= $status->cash
                ) {
                    $newStatus = $status;
                }
                else {
                    break;
                }
            }
            echo("id: {$user->id}, tops: $paymentsSum {$user->cur}, ref" . ($user->parent_id ? '+' : '-') . ", status: {$user->premium_status_id} -> {$newStatus->id}" . PHP_EOL);
            $user->update(['premium_status_id' => $newStatus->id]);
            $user->refresh();
        }
        return 0;
    }
}
