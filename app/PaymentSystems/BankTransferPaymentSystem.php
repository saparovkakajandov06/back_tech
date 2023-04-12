<?php

namespace App\PaymentSystems;

use App\Transaction;

class BankTransferPaymentSystem extends AbstractPayOpPaymentSystem
{
    protected string $name = 'BankTransfer';

    protected array $availableCurrencies = [
        Transaction::CUR_EUR
    ];

    protected function getPaymentMethodId(?string $country = null): int
    {
        return match($country) {
            'ES' => 200022,
            'GB' => 203801,
            'PT' => 200023,

            //Banks
            default => 200018
        };
    }
}