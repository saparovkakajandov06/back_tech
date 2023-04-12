<?php

namespace App\PaymentSystems;

use App\Transaction;

class EWalletPaymentSystem extends AbstractPayOpPaymentSystem
{
    protected string $name = 'EWallet';

    protected array $availableCurrencies = [
        Transaction::CUR_EUR,
        Transaction::CUR_USD
    ];

    protected function getPaymentMethodId(?string $country = null): int
    {
        return match($country) {
            //QIWI - because Adv Cash is not supporting for USA
            'US' => 5101,

            //Advanced Cash
            default => 765
        };
    }
}