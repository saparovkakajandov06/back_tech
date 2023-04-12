<?php

namespace App\PaymentSystems;

use App\Payment;
use App\Services\Money\Services\TransactionsService;
use App\Transaction;
use Illuminate\Support\Str;

class FakePaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'Fake';
    protected array $availableCurrencies = Transaction::CUR;

    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        resolve(TransactionsService::class)->create(
            user: $localPayment->user,
            type: Transaction::INFLOW_PAYMENT,
            amount: $localPayment->amount,
            cur: $localPayment->currency,
            comment: 'Пополнение через Fake ' . $localPayment->id,
        );
        $localPayment->status = Payment::STATUS_PAYMENT_SUCCEEDED;
        $localPayment->save();
        return [
            'id' => Str::random(),
            'url' => $val['success_url'],
        ];
    }

    protected function getCheckoutUrl(array $fakePayment): string
    {
        return $fakePayment['url'];
    }

    protected function getRemotePaymentId(array $fakePayment): string
    {
        return $fakePayment['id'];
    }

    public function mapRequestToStatus($request): string
    {
        return '';
    }

    public function getForeignPaymentId($request): string
    {
        return '';
    }
}
