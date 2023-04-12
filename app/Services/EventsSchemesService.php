<?php

namespace App\Services;

use App\Domain\Models\CompositeOrder;
use App\EventsScheme;
use App\Services\SendpulseService;
use App\Transaction;
use App\User;
use Illuminate\Support\Carbon;

class EventsSchemesService
{
    private SendpulseService $sps;

    public function __construct(SendpulseService $sps)
    {
        $this->sps = $sps;
    }

    public function createUnpaidOrder($userId, $data): void
    {
        EventsScheme::createUnpaidOrder($userId, $data);
    }

    public function checkUnpaidOrder(EventsScheme $es): void
    {
        $user = User::find($es->user_id);

        if($user and $es->updated_at > Carbon::now()->subMinutes(30)){
            return;
        }

        $userNewTransaction = Transaction::where('user_id', $es->user_id)
            ->where('created_at', '>', $es->created_at)
            ->whereIn('type', [
                Transaction::INFLOW_PAYMENT,
                Transaction::OUTFLOW_ORDER
            ])
            ->count();

        if(!$userNewTransaction){
            $this->sps->sendUnpaidOrder($es);
        }
        $es->delete();
    }

    public function createNotTopUpBalance($userId, $data): void
    {   
        EventsScheme::createNotTopUpBalance($userId, $data);
    }

    public function checkNotTopUpBalance(EventsScheme $es): void
    {
        if($es->updated_at > Carbon::now()->subMinutes(30)){
            return;
        }

        $userId = $es->user_id;

        $userNewTransaction = Transaction::where('user_id', $userId)
            ->where('created_at', '>', $es->created_at)
            ->where('type', Transaction::INFLOW_PAYMENT)
            ->count();

        if(!$userNewTransaction){
            $this->sps->sendNotTopUpBalance($es);
        }
        $es->delete();
    }

    public function createSuccededPayment(Transaction $transaction): void
    {
        $userId = $transaction->user_id;

        EventsScheme::createSuccededPayment($userId, $transaction);    

    }

    public function checkSuccededPayment(EventsScheme $es): void
    {
        $this->sps->sendSuccededPayment($es);
        $es->delete();
    }

    public static function createAbandonedСart($userId, $data)
    {
        EventsScheme::createAbandonedСart($userId, $data);
    }

    public function checkAbandonedСart(EventsScheme $es)
    {
        if($es->data == []){
            $es->delete();
            return;
        }
        
        if($es->updated_at > Carbon::now()->subMinutes(30)){
            return;
        }

        $ordersCount = CompositeOrder::where('user_id', $es->user_id)
            ->where('created_at', '>', $es->updated_at)
            ->count();

        if($ordersCount < 1){
            $this->sps->sendAbandonedСart($es);
        }

        $es->delete();
    }

    public function checkEventForSend()
    {
        $ess = EventsScheme::get();

        if($ess == '[]'){
            return;
        }

        foreach($ess as $es){
            switch($es['event_name']){
                case EventsScheme::NOT_TOP_UP_BALANCE:
                    $this->checkNotTopUpBalance($es);
                    break;
                case EventsScheme::UNPAID_ORDER:
                    $this->checkUnpaidOrder($es);
                    break;
                case EventsScheme::SUCCEDED_PAYMENT:
                    $this->checkSuccededPayment($es);
                    break;
                case EventsScheme::ABANDONED_CART:
                    $this->checkAbandonedСart($es);
                    break;
                default:
                    break;
            }
        }
    }
}

?>