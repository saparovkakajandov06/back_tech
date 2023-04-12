<?php

namespace App\Providers;

use App\Observers\TransactionObserver;
use App\Transaction;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        Registered::class => [
//            SendEmailVerificationNotification::class,
//        ],

// ----------------- in -------------
//        'App\Events\Money\InflowTest' => [
//            'App\Listeners\Money\SaveTransaction',
//        ],
//        'App\Events\Money\InflowPayment' => [
//            'App\Listeners\Money\SaveTransaction',
//            'App\Listeners\Money\UpdatePremiumStatus',
//        ],
//        'App\Events\Money\InflowCreate' => [
//            'App\Listeners\Money\SaveTransaction',
//        ],
//        'App\Events\Money\InflowUserJob' => [
//            'App\Listeners\Money\SaveTransaction',
//        ],
//        'App\Events\Money\InflowRefBonus' => [
//            'App\Listeners\Money\SaveTransaction',
//        ],
//        'App\Events\Money\InflowRefund' => [
//            'App\Listeners\Money\SaveTransaction',
//        ],

// --------------- out --------------
//        'App\Events\Money\OutflowTest' => [
//            'App\Listeners\Money\SaveTransaction',
//        ],
//        'App\Events\Money\OutflowOrder' => [
//            'App\Listeners\Money\SaveTransaction',
////            'App\Listeners\RefBonus',
//        ],
//        'App\Events\Money\OutflowOther' => [
//            'App\Listeners\Money\SaveTransaction',
//        ],
//        'App\Events\Money\OutflowWithdraw' => [
//            'App\Listeners\Money\SaveTransaction',
//            'App\Listeners\Money\SaveWithdraw',
//        ],

        // ---------- orders --------
//        'App\Events\ChangeOrderStatus' => [
//            'App\Listeners\SaveOrderStatus',
//        ],

    ];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Transaction::observe(TransactionObserver::class);
    }
}
