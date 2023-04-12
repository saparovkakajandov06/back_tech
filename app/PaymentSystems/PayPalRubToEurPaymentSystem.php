<?php

namespace App\PaymentSystems;

use App\Payment;
use App\Services\CurrencyService;
use App\Transaction;

class PayPalRubToEurPaymentSystem extends PayPalPaymentSystem
{
    protected string $name = 'PayPal RubToEur';

    protected array $availableCurrencies = [
        Transaction::CUR_RUB,
    ];

    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $currencyService = resolve(CurrencyService::class);

        $val['amount'] = $currencyService->convert($val['cur'], Transaction::CUR_EUR, $val['amount']);
        $val['cur'] = Transaction::CUR_EUR;

        return parent::createRemotePayment($localPayment, $val);
    }
}
