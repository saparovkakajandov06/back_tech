<?php

namespace App\PaymentSystems;

use App\Payment;
use App\Services\CurrencyService;
use App\Transaction;

class PayPalRubToUsdPaymentSystem extends PayPalPaymentSystem
{
    protected string $name = 'PayPal RubToUsd';

    protected array $availableCurrencies = [
        Transaction::CUR_RUB,
    ];

    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $currencyService = resolve(CurrencyService::class);

        $val['amount'] = $currencyService->convert($val['cur'], Transaction::CUR_USD, $val['amount']);
        $val['cur'] = Transaction::CUR_USD;

        return parent::createRemotePayment($localPayment, $val);
    }
}
