<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsScheme extends Model
{
    use HasFactory;

    const UNPAID_ORDER = 'unpaidorder';
    const NOT_TOP_UP_BALANCE = 'nottopupbalance';
    const SUCCEDED_PAYMENT = 'succededpayment';
    const ABANDONED_CART = 'abandonedcart';

    protected $casts = [
        'data' => 'array'
    ];

    protected $fillable = [
        'user_id',
        'event_name',
        'created_at',
        'updated_at',
        'data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public static function createUnpaidOrder($userId, $data)
    {
        self::createEvent($userId, $data, self::UNPAID_ORDER);
    }

    public static function createNotTopUpBalance($userId, $data)
    {
        self::createEvent($userId, $data, self::NOT_TOP_UP_BALANCE);
    }

    public static function createSuccededPayment($userId, $data)
    {
        self::createEvent($userId, $data, self::SUCCEDED_PAYMENT);
    }

    public static function createAbandonedСart($userId, $data)
    {
        self::createEvent($userId, $data, self::ABANDONED_CART);
    }

    public static function createEvent($userId, $data, $eventName)
    {
        $event = self::where([
            'user_id' => $userId,
            'event_name' => $eventName
        ])->first();
        
        if(!$event) {
            self::create([
                'user_id' => $userId,
                'event_name' => $eventName,
                'data' => $data
            ]);
        } elseif($eventName != self::SUCCEDED_PAYMENT) {
            $event->data = $data;
            $event->save();
        }

        //deleted abandoned cart if user click pay btn
        if($eventName === self::UNPAID_ORDER){
            $eventAC = self::where([
                'user_id' => $userId,
                'event_name' => self::ABANDONED_CART
            ])->first();
            if(isset($eventAC)){
                $eventAC->delete();
            }
        }
    }

}