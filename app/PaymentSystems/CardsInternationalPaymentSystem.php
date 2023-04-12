<?php

namespace App\PaymentSystems;

use App\Transaction;

class CardsInternationalPaymentSystem extends AbstractPayOpPaymentSystem
{
    protected string $name = 'CardsInternational';

    protected array $availableCurrencies = [
        Transaction::CUR_USD,
        Transaction::CUR_EUR
    ];

    protected function getPaymentMethodId(?string $country = null): int
    {
        return 381;
    }
}