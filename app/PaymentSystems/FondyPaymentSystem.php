<?php

namespace App\PaymentSystems;

use App\Payment;
use App\Transaction;
use Cloudipsp\Checkout as fondyCheckout;
use Cloudipsp\Configuration as fondyConfig;

class FondyPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'Fondy';
    protected array $availableCurrencies = [
        Transaction::CUR_RUB,
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
        Transaction::CUR_BRL,
        Transaction::CUR_UAH,
    ];
    /**
     * https://docs.fondy.eu/en/docs/page/28/
     * The callback is considered to be processed successfully by the merchant if HTTP 200 OK status returned.
     * Otherwise FONDY will repeat callback attempts with such time intervals: 2, 60, 300, 600, 3600, 86400 seconds.
     */

    const REMOTE_SUCCEEDED = 'approved';

    public function __construct()
    {
        fondyConfig::setMerchantId(env('FONDY_MERCHANT_ID'));
        fondyConfig::setSecretKey(env('FONDY_SECRET_KEY'));

        parent::__construct();
    }

    /* example result: [
       "checkout_url" => "https://pay.fondy.eu/merchants/6334f4517c1c343986b05c62ff2b7130527e7503/default/index.html?token=c9d785fe07443c5c68c4bb3aa53a1c01ffcfbd04",
       "payment_id" => "458335733",
       "response_status" => "success",
    ] */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $response = fondyCheckout::url([
            'amount' => (int)round($val['amount'] * 100, 0),
            'currency' => $val['cur'],
            'lang' => $val['locale'],
            'lifetime' => 36000,
            'description' => $this->getRemoteDescription($val, $localPayment),
            'order_id' => $localPayment->id,
            'response_url' => env('FONDY_REDIRECT_URL') . '?to=' . urlencode($val['success_url']),
            'server_callback_url' => env('FONDY_HOOK_URL'),
        ])->getData();

        return [
            'id' => $this->getRemotePaymentId($response),
            'url' => $this->getCheckoutUrl($response),
        ];
    }

    protected function getCheckoutUrl(array $fondyPayment): string
    {
        return $fondyPayment['checkout_url'];
    }

    protected function getRemotePaymentId(array $fondyPayment): string
    {
        return $fondyPayment['payment_id'];
    }

    public function getForeignPaymentId($request): string
    {
        return data_get($request->all(), 'payment_id');
    }

    public function mapRequestToStatus($request): string
    {
        $status = data_get($request, 'order_status');
        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            default => $status,
        };
    }
}
