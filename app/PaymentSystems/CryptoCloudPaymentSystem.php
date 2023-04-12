<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Services\CurrencyService;
use App\Transaction;
use Illuminate\Http\Client\PendingRequest;


class CryptoCloudPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'CryptoCloud';
    protected array $availableCurrencies = [
        Transaction::CUR_RUB,
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
        Transaction::CUR_UZS
    ];

    const REMOTE_SUCCEEDED = 'success';
    const REMOTE_CANCELED = 'fail';

    protected PendingRequest $client;

    protected string $shopId;

    public function __construct()
    {
        $this->client = new PendingRequest();
        $this->client->baseUrl('https://api.cryptocloud.plus/v1')
            ->acceptJson()
            ->asJson()
            ->retry(3, 300)
            ->withHeaders(['Authorization' => 'Token ' . env('CCLOUD_API_KEY')]);

        parent::__construct();
    }

    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        if ($val['cur'] === Transaction::CUR_UZS) {
            $converter = app(CurrencyService::class);

            $val['amount'] = $converter->convert($val['cur'], 'USD', $val['amount']);
            $val['amount'] = round($val['amount'], 2);
            $val['cur'] = 'USD';
        }

        $response = $this->client->post(
            'invoice/create',
            [
                'shop_id' => env('CCLOUD_SHOP_ID'),
                'amount' => $val['amount'],
                'order_id' => $localPayment->id,
                'currency' => $val['cur'],
            ]
        )->json();

        if (!array_key_exists('status', $response) || $response['status'] !== self::REMOTE_SUCCEEDED) {
            throw (new ReportableException('Remote payment creation error'))
                ->withData(['exception' => json_encode($response)]);
        }

        return [
            'id' => $this->getRemotePaymentId($response),
            'url' => $this->getCheckoutUrl($response),
        ];
    }

    protected function getCheckoutUrl($paymentResponse): string
    {
        return data_get($paymentResponse, 'pay_url');
    }

    protected function getRemotePaymentId($paymentResponse): string
    {
        return data_get($paymentResponse, 'invoice_id');
    }

    public function getForeignPaymentId($request): string
    {
        return data_get($request->all(), 'invoice_id');
    }

    public function mapRequestToStatus($request): string
    {
        $status = data_get($request, 'status');
        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_CANCELED => Payment::STATUS_PAYMENT_CANCELED,
            default => $status,
        };
    }
}
