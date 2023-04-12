<?php

namespace App\PaymentSystems;

use App\Transaction;

class PoliPaymentSystem extends AbstractPayOpPaymentSystem
{
    protected string $name = 'Poli';

    protected array $availableCurrencies = [
        Transaction::CUR_USD
    ];

    protected function getPaymentMethodId(?string $country = null): int
    {
        return 379;
    }
}