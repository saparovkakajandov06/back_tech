<?php

namespace App\PaymentSystems;

use App\Transaction;

class RevolutPaymentSystem extends AbstractPayOpPaymentSystem
{
    protected string $name = 'Revolut';

    protected array $availableCurrencies = [
        Transaction::CUR_EUR
    ];

    protected function getPaymentMethodId(?string $country = null): int
    {
        return 3822;
    }
}