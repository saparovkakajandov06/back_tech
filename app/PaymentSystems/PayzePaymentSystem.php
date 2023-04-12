<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

class PayzePaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    const REMOTE_SUCCEEDED = 'Committed';
    const REMOTE_OPENED = 'Blocked';
    const REMOTE_FAILED = 'Rejected';

    const API_URL = 'https://payze.io/api/v1';

    protected string $name = 'Payze';
    protected array $availableCurrencies = [
        Transaction::CUR_USD,
        Transaction::CUR_UZS,
    ];

    protected string $apiKey;
    protected string $apiSecret;

    protected LoggerInterface $log;
    protected array $hookRequest;

    public function __construct(
        string $apiKey,
        string $apiSecret,
    )
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;

        parent::__construct();
    }

    /*
     * https://docs.payze.io/reference/just-pay-with-product-info
     * Create payment
     */
    /**
     * @throws ReportableException
     */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $data = [
            'method' => 'justPay',
            'apiKey' => $this->apiKey,
            'apiSecret' => $this->apiSecret,

            'data' => [
                'amount' => round((float)$val['amount'], 2),
                'currency' => $val['cur'],
                'callback' => $val['success_url'],
                'callbackError' => $val['cancel_url'],
                'preauthorize' => false,
                'lang' => in_array($val['locale'], ['ru', 'uk']) ? 'RU' : 'EN',
                'hookUrl' => route('payze_hook', [], true) . '?paymentId=' . $localPayment->id,
                'hookRefund' => false,

                'info' => [
                    'description' => $this->getRemoteDescription($val, $localPayment),
                    'name' => $this->getRemoteDescription($val, $localPayment),
                ],
            ]
        ];

        $this->debugLog('Payze api request is created', [
            'data' => $data
        ]);

        $response = Http::asJson()->post(self::API_URL, $data);
        $responseData = $response->json();

        $this->debugLog('Payze api response', [
            'response' => $response,
            'responseData' => $responseData
        ]);

        $this->debugLog('Payze order is created', [
            'paymentId' => $localPayment->id,
            'paymentForeignId' => $localPayment->foreign_id,
            'transactionId' => $this->getRemotePaymentId($responseData)
        ]);

        return [
            'id' => $this->getRemotePaymentId($responseData),
            'url' => $this->getCheckoutUrl($responseData),
        ];
    }

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        return __('payment.order_payment', ['payment_id' => $localPayment->id]);
    }

    protected function getRemotePaymentId(array $response): string
    {
        $id = data_get($response, 'response.transactionId');

        if (!$id) {
            throw (new ReportableException('Param transactionId is not found at Payze api request'))
                ->withData(['exception' => json_encode($response)]);
        }

        return $id;
    }

    protected function getCheckoutUrl(array $response): string
    {
        $url = data_get($response, 'response.transactionUrl');

        if (!$url) {
            throw (new ReportableException('Param transactionUrl is not found at Payze api request'))
                ->withData(['exception' => json_encode($response)]);
        }

        return $url;
    }

    public function getForeignPaymentId($request): string
    {
        return data_get($this->hookRequest, 'transactionId');
    }

    /**
     * https://docs.payze.io/reference/webhooks
     * @param Request $request
     * @return string
     */
    public function mapRequestToStatus($request): string
    {
        $status = data_get($this->hookRequest, 'status');

        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_FAILED => Payment::STATUS_PAYMENT_CANCELED,

            default => $status,
        };
    }

    public function handleHook(Request $request)
    {
        $this->hookRequest = $request->all();

        $this->debugLog('Payze handleHook', ['r' => $this->hookRequest]);

        return parent::handleHook($request);
    }

    protected function tryUpdatePayment(Payment $payment, string $status): bool
    {
        if ($status === self::REMOTE_OPENED) {
            $this->debugLog('Execute Payze payment ...', [
                'id' => $payment->foreign_id
            ]);

            $foreignId = $payment->foreign_id;

            //Send commit to payzee
            $data = [
                'method' => 'commit',
                'apiKey' => $this->apiKey,
                'apiSecret' => $this->apiSecret,

                'data' => [
                    'amount' => $payment->amount,
                    'transactionId' => $foreignId
                ]
            ];

            $response = Http::asJson()->post(self::API_URL, $data);
            $responseData = $response->json();

            $this->debugLog('Payze checkout order response', ['response' => $response, 'responseData' => $responseData]);

            if (!data_get($response, 'response.status')) {
                throw (new ReportableException('Execute Payze payment error'))
                    ->withData(['exception' => json_encode($response)]);
            }
        }

        return parent::tryUpdatePayment($payment, $status);
    }
}
