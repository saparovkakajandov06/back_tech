<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Transaction
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $event_id
 * @property string $type
 * @property string $amount
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $related_user_id
 * @property string $commission
 * @property string $cur
 * @property-read \App\User $user
 * @method static \Database\Factories\TransactionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCur($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereRelatedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
 * @mixin \Eloquent
 */
class Transaction extends BaseModel {

    use HasFactory;

    // ------- заработано --------------------
    const GROUP_EARNED = 'GROUP_EARNED';
    // бонус
    const INFLOW_REF_BONUS = 'INFLOW_REF_BONUS';
    // задание
    const INFLOW_USER_JOB = 'INFLOW_USER_JOB';


    // ------------------ снято ----------------------
    const GROUP_WITHDRAWN = 'GROUP_WITHDRAWN';
    const OUTFLOW_WITHDRAWAL = 'OUTFLOW_WITHDRAWAL';


    // ----------------- внесено -----------------------
    const GROUP_DEPOSITED = 'GROUP_DEPOSITED';
    const INFLOW_TEST = 'INFLOW_TEST';
    const INFLOW_OTHER = 'INFLOW_OTHER';
    // деньги из воздуха
    const INFLOW_CREATE = 'INFLOW_CREATE';
    // возврат
    const INFLOW_REFUND = 'INFLOW_REFUND';
    // через платежную систему
    const INFLOW_PAYMENT = 'INFLOW_PAYMENT';


    // --------- остальное -------------
    const GROUP_UNKNOWN = 'GROUP_UNKNOWN';
    const UNKNOWN = 'UNKNOWN';
    const OUTFLOW_TEST = 'OUTFLOW_TEST';
    const OUTFLOW_OTHER = 'OUTFLOW_OTHER';
    const OUTFLOW_ORDER = 'OUTFLOW_ORDER';
    const OUTFLOW_CANCEL_REF_BONUS = 'OUTFLOW_CANCEL_REF_BONUS';
    const OUTFLOW_CANCEL_REFUND = 'OUTFLOW_CANCEL_REFUND';

    // действие администратора
    const OUTFLOW_DESTROY = 'OUTFLOW_DESTROY';

    const CUR_RUB = 'RUB';
    const CUR_USD = 'USD';
    const CUR_EUR = 'EUR';
    const CUR_TRY = 'TRY';
    const CUR_BRL = 'BRL';
    const CUR_UAH = 'UAH';
    const CUR_UZS = 'UZS';

    const NOT_ENOUGH_FUNDS = 'У вас недостаточно средств на счете.';

    const CUR = [
        self::CUR_RUB,
        self::CUR_USD,
        self::CUR_EUR,
        self::CUR_TRY,
        self::CUR_BRL,
        self::CUR_UAH,
        self::CUR_UZS,
    ];

    const TRANSACTION_GROUP = [
        self::GROUP_DEPOSITED => [
            self::INFLOW_TEST, 
            self::INFLOW_OTHER, 
            self::INFLOW_CREATE, 
            self::INFLOW_REFUND, 
            self::INFLOW_PAYMENT
        ],
        self::GROUP_WITHDRAWN => [
            self::OUTFLOW_WITHDRAWAL
        ],
        self::GROUP_EARNED => [
            self::INFLOW_REF_BONUS, 
            self::INFLOW_USER_JOB
        ],
    ];

    const TYPES = [
        self::INFLOW_PAYMENT, // пополнение
        self::INFLOW_REFUND, // возврат
        self::INFLOW_REF_BONUS, // реферальный бонус

        self::INFLOW_CREATE, // создано (админом)
        self::INFLOW_OTHER, // другое

        self::INFLOW_USER_JOB, // заработок (на будущее)
        self::INFLOW_TEST, // для тестов

        self::OUTFLOW_ORDER, // оплата заказа
        self::OUTFLOW_CANCEL_REF_BONUS, // отмена реферального бонуса
        self::OUTFLOW_CANCEL_REFUND, // отмена возврата

        self::OUTFLOW_DESTROY, // уничтожено (админом)

        self::OUTFLOW_WITHDRAWAL, // вывод средств (на будущее)
        self::OUTFLOW_TEST, // для тестов
        self::OUTFLOW_OTHER, // другое
        self::UNKNOWN, // неизвестно
    ];

    protected $casts = [
        'type' => 'string',
        'order_ids' => 'array',
        'payment_id' => 'int',
    ];

    public static function allZeros(): array
    {
        return array_map(fn($type) => [
            'type' => $type,
            'amount' => 0.00,
        ], self::TYPES);
    }

    public static function insertTotalsData(array $zeros, array $data): array
    {
        $data = collect($data);
        $res = collect();

        foreach($zeros as $z) {
            $res[] = $data->where('type', $z['type'])->first() ?? $z;
        }

        return $res->sortBy('type')->values()->all();
    }

    public static function withZeros(array $data): array
    {
        return self::insertTotalsData(self::allZeros(), $data);
    }

    // ------------------------------------------

    public function user() {
        return $this->belongsTo('App\User');
    }
}
