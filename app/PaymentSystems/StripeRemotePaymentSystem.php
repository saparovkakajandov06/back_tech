<?php

namespace App\PaymentSystems;

use App\Payment;
use App\Transaction;
use Illuminate\Support\Facades\Http;

class StripeRemotePaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'StripeRemote';

    protected array $availableCurrencies = [
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
    ];

    const REMOTE_SUCCEEDED = 'charge.succeeded';
    const API_PATH = '/stripe_foreign/create/';

    public const USE_FOR_APP = true;
    public const ICON_FOR_APP = 'stripe/265d64e93f8b1524b07e9f9f6404f233.svg';

    protected string $url;
    protected string $metaKey;
    protected bool $test;

    protected array $event;

    protected string $paymentMethodId = 'card';

    public function __construct(
        string $url,
        string $metaKey,
        bool $test,
    ) {
        $this->url = $url;
        $this->metaKey = $metaKey;
        $this->test = $test;

        parent::__construct();
    }

    /**
     * For example 'card' or 'sepa_debit'
     * @param string $methodId
     */
    public function setPaymentMethod(string $methodId)
    {
        $this->paymentMethodId = $methodId;
    }

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        return __('payment.order_payment', ['payment_id' => $localPayment->id]);
    }

    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $data_order = [
            'cancel_url'          => $val['cancel_url'],
            'mode'                => 'payment',
            'success_url'         => $val['success_url'],
            'client_reference_id' => $localPayment->id,
            'payment_intent_data' => [
                'metadata' => [
                    $this->metaKey => route('stripe:callback')
                ]
            ],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($val['cur']),
                    'product_data' => [
                        'name' => $this->getRemoteDescription($val, $localPayment),
                    ],
                    'unit_amount' => (int)round($val['amount'] * 100, 0, PHP_ROUND_HALF_DOWN),
                ],
                'quantity' => 1,
            ]],
            'payment_method_types' => [$this->paymentMethodId],
        ];
        $httpResp = Http::withBasicAuth('1', '123')
            ->withOptions(['base_uri' => $this->url])
            ->asJson()
            ->post(self::API_PATH . ($this->test ? '?test' : ''), $data_order);

        $httpResp_data = $httpResp->json(); // чтобы отлаживать ранее - сообщения об ошибках

        if (!$httpResp->successful() && isset($httpResp_data['error']) && isset($httpResp_data['message']))
            throw new \Exception($httpResp_data['message']);

        return [
            'id'  => $this->getRemotePaymentId($httpResp_data),
            'url' => $this->getCheckoutUrl($httpResp_data)
        ];
    }

    protected function getCheckoutUrl(array $stripePayment): string
    {
        return $stripePayment['url'];
    }

    protected function getRemotePaymentId(array $stripePayment): string
    {
        return $stripePayment['payment_intent'];
    }

    public function getForeignPaymentId($request): string
    {
        return match ($this->event['data']['object']['object']) {
            'charge',
            'checkout.session' => $this->event['data']['object']['payment_intent'],
            'payment_intent'   => $this->event['data']['object']['id'],
            default => ''
        };
    }

    public function mapRequestToStatus($request): string
    {
        $this->event = $request->json()->all();
        $status = $this->event['type'];

        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            default => $status,
        };
    }
}
