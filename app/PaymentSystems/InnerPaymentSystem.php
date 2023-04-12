<?php

namespace App\PaymentSystems;

use App\Domain\Models\CompositeOrder;
use App\Domain\Models\Money\UserBalance;
use App\Exceptions\NonReportable\InsufficientFundsException;
use App\Payment;
use App\Services\Money\PaymentService;
use App\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class InnerPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'Inner';
    protected array $availableCurrencies = Transaction::CUR;

    public function startOrderSession(array $val): array {
        if (!isset($val['order_ids'], $val['amount'])) {
            /* TODO: Throw error in feature*/
            return [];
        }

        $user = Auth::user();

        $userBalance = UserBalance::forUser(
            $user,
            $val['cur']
        );

        if ($val['amount'] > $userBalance->val()) {
            throw new InsufficientFundsException();
        }

        $orders = CompositeOrder::whereIn('id', $val['order_ids'])
            ->get()->all();


        // Automatically runs orders
        $payment = app(PaymentService::class)->create($this, $user, $val['amount'], $val, $orders, true, false);

        return [
            'id' => $payment['payment']->id,
            'url' => $val['success_url']
        ];
    }

    public function createRemotePayment(Payment $payment, array $paymentData): array
    {
        return [
            'id' => $payment->id,
            'url' => ''
        ];
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
