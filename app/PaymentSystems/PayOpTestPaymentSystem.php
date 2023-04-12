<?php

namespace App\PaymentSystems;

use App\Transaction;

class PayOpTestPaymentSystem extends AbstractPayOpPaymentSystem
{
    protected string $name = 'PayOpTest';

    protected array $availableCurrencies = [
        Transaction::CUR_EUR,
        Transaction::CUR_RUB,
        Transaction::CUR_USD
    ];

    protected function getPaymentMethodId(?string $country = null): int
    {
        //Qiwi
        return 5101;
    }
}