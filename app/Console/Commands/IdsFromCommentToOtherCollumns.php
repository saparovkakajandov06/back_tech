<?php

namespace App\Console\Commands;

use App\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class IdsFromCommentToOtherCollumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:ids_from_comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order and payment ids from transactions.comment to transaction.order_ids and transactions.payment_id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $transactionsOld = Transaction::whereNull(['payment_id', 'order_ids'])
            ->whereNotNull('comment')
            ->count();


        foreach(Transaction::whereNull(['payment_id', 'order_ids'])
            ->whereNotNull('comment')
            ->lazyById(100, $column = 'id') as $transaction) {
                $getTypeAndIds = $this->getTypeAndIds($transaction->comment);
                if(isset($getTypeAndIds)){
                    $column = $getTypeAndIds['type'];
                    $transaction->$column = $getTypeAndIds['id'];
                    $transaction->save();

                    echo "\n";
                    echo $transaction->id;
                }
        }


        $transactionsNow = Transaction::whereNull(['payment_id', 'order_ids'])
            ->whereNotNull('comment')
            ->count();
        
        echo "\n[OLD]{$transactionsOld}\n[NOW]{$transactionsNow}\n";
    }

    private function getTypeAndIds($input)
    {
        if(Str::contains($input, 'Пополнение через')){
            $type = 'payment_id';
            $pattern = "/([0-9]{1,10})/";

            if (preg_match($pattern, $input, $matches) === 1) {
                [ $full, $paymentId] = $matches;
                return [
                    'type' => $type,
                    'id' => $paymentId
                ];
            } else {
                return null;
            }
        } elseif(
            Str::contains($input, 'Заказ') or Str::contains($input, 'Оплата заказа') 
            or Str::contains($input, 'Бонус за заказ')){
                
            $type = 'order_ids';
            $pattern = "/(Заказ|Оплата\sзаказа|Бонус\sза\sзаказ)\s([0-9]{1,10})/";

            if (preg_match($pattern, $input, $matches) === 1) {
                [ $full, $order, $orderId] = $matches;
                return [
                    'type' => $type, 
                    'id' => [(int) $orderId]
                ];
            } else {
                return null;
            }
        } 
    }


}
