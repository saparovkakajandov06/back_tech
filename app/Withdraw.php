<?php

namespace App;

/**
 * App\Withdraw
 */
class Withdraw extends BaseModel {

    protected $table = 'withdraw';

    const BANK_CARD = 'BANK_CARD';
    const YANDEX_MONEY = 'YANDEX_MONEY';
    const QIWI_WALLET = 'QIWI_WALLET';
    const PHONE_NUMBER = 'PHONE_NUMBER';

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

}
