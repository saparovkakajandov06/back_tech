<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Transaction;
use Illuminate\Support\Facades\Http;

class CentPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'Cent';
    protected array $availableCurrencies = [
        Transaction::CUR_RUB,
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
    ];

    protected string $token;
    protected string $shopId;

    const REMOTE_SUCCEEDED = 'SUCCESS';
    const REMOTE_FAILED = 'FAIL';
    const API_URL = 'https://cardlink.link/api/v1/bill/create';

    public function __construct()
    {
        $this->token = env('CENT_TOKEN');
        $this->shopId = env('CENT_SHOP_ID');

        parent::__construct();
    }

    /* example result from https://cent.app/en/reference/api#bill-create : {
        "success": "true",
        "link_url": "https://cent.app/link/3P1p2rgW7Y",
        "link_page_url": "https://cent.app/transfer/3P1p2rgW7Y",
        "bill_id": "3P1p2rgW7Y"
    } */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $data = [
            'amount' => $val['amount'],
            'currency_in' => $val['cur'],
            'description' => $this->getRemoteDescription($val, $localPayment),
            'name' => substr($val['description'] ?? '', 0, 250),
            'order_id' => $localPayment->id,
            'payer_pays_commission' => 0,
            'shop_id' => $this->shopId,
            'type' => 'normal'
        ];
        $response = Http::withToken($this->token)->post(self::API_URL, $data)->json();
        if ($response['success'] !== 'true') {
            throw (new ReportableException('Remote payment creation error'))
                ->withData(['exception' => json_encode($response)]);
        }
        return [
            'id' => $this->getRemotePaymentId($response),
            'url' => $this->getCheckoutUrl($response),
        ];
    }

    protected function getCheckoutUrl(array $centPayment): string
    {
        return $centPayment['link_page_url'];
    }

    protected function getRemotePaymentId(array $centPayment): string
    {
        return $centPayment['bill_id'];
    }

    /* example result from https://cent.app/en/reference/api#bill-create : {
        "success": "true",
        "link_url": "https://cent.app/link/3P1p2rgW7Y",
        "link_page_url": "https://cent.app/transfer/3P1p2rgW7Y",
        "bill_id": "3P1p2rgW7Y"
    } */
    public function getForeignPaymentId($request): string
    {
        return data_get($request->all(), 'TrsId');
    }

    public function mapRequestToStatus($request): string
    {
        $status = data_get($request, 'Status');
        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_FAILED => Payment::STATUS_PAYMENT_CANCELED,
            default => $status,
        };
    }
}
