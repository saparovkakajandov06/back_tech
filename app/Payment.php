<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Платежи через внешние системы
 */
class Payment extends Model
{
    use HasFactory;

    const ACTION_TYPE_TRANSACTION = "TYPE_TRANSACTION";
    const ACTION_TYPE_CASHBACK = "TYPE_CASHBACK";

    const TYPE_YANDEX_KASSA = 'YANDEX_KASSA';
    const TYPE_STRIPE = 'STRIPE';
    const TYPE_PAYPAL = 'PAYPAL';
    const TYPE_CLOUD_PAYMENTS = 'CLOUD_PAYMENTS';
    const TYPE_TINKOFF = 'TINKOFF';

    const STATUS_PAYMENT_WAITING_FOR_CAPTURE = 'payment.waiting_for_capture';
    const STATUS_PAYMENT_SUCCEEDED = 'payment.succeeded';
    const STATUS_PAYMENT_CANCELED = 'payment.canceled';
    const STATUS_PAYMENT_AMOUNT_MISMATCHED = 'payment.mismatched';
    const STATUS_REFUND_SUCCEEDED = 'refund.succeeded';
    const STATUS_PENDING = 'pending';

    const TERMINAL_STATUSES = [
        self::STATUS_PAYMENT_SUCCEEDED
    ];

    const PAYPAL_PAYMENT_APPROVED = 'CHECKOUT.ORDER.APPROVED';

    const CLOUD_PAYMENTS_APPROVED = 'Completed';
    const CLOUD_PAYMENTS_DECLINED = 'Declined';
    const CLOUD_PAYMENTS_CANCELLED = 'Cancelled';

    //Деньги захолдированы на карте клиента ожидается подтверждение операции
    const TINKOFF_STATUS_AUTHORIZED = 'AUTHORIZED';
    //Операция подтверждена
    const TINKOFF_STATUS_CONFIRMED = 'CONFIRMED';
    //Списание денежных средств закончилась ошибкой
    const TINKOFF_STATUS_REJECTED = 'REJECTED';

    protected $guarded = [];

    protected $casts = [
        'order_ids' => 'array',
        'actions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('\App\User');
    }
}
