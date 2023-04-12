<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Cashback extends BaseModel {

    use HasFactory;

    protected $table = 'cashback';


    // ------------------ снято ----------------------
    const CASHBACK_OUTFLOW_ORDER = 'CASHBACK_OUTFLOW_ORDER';


    // ----------------- внесено -----------------------
    const CASHBACK_INFLOW_CREATE = 'CASHBACK_INFLOW_CREATE';
    const CASHBACK_INFLOW_PAYMENT = 'CASHBACK_INFLOW_PAYMENT';


    const CUR_RUB = 'RUB';
    const CUR_USD = 'USD';
    const CUR_EUR = 'EUR';
    const CUR_TRY = 'TRY';
    const CUR_BRL = 'BRL';
    const CUR_UAH = 'UAH';

    const NOT_ENOUGH_FUNDS = 'У вас недостаточно средств на счете кешбека.';

    const CUR = [
        self::CUR_RUB,
        self::CUR_USD,
        self::CUR_EUR,
        self::CUR_TRY,
        self::CUR_BRL,
        self::CUR_UAH,
    ];


    const TYPES = [
        self::CASHBACK_INFLOW_CREATE, // пополнение админосм
        self::CASHBACK_INFLOW_PAYMENT, // пополнение админосм

        self::CASHBACK_OUTFLOW_ORDER, // оплата заказа
    ];

    protected $casts = [
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
        return $this->belongsTo(User::class);
    }
}
