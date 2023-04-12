<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayPalPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'PayPal';

    protected array $availableCurrencies = [
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
    ];

    protected bool $sandbox;
    protected string $apiBaseUrl;
    protected string $clientId;
    protected string $secret;
    protected PaymentSystemProxy $proxy;

    protected array $hookRequest;

    //Checkout payment is created and approved by buyer.
    //https://developer.paypal.com/api/rest/webhooks/event-names/#v2
    const REMOTE_SUCCEEDED = 'CHECKOUT.ORDER.APPROVED';

    const SANDBOX_URL = 'https://api-m.sandbox.paypal.com';
    const LIVE_URL = 'https://api-m.paypal.com';


    public function __construct(
        bool $sandbox,
        string $clientId,
        string $secret,
        PaymentSystemProxy $proxy,
    ) {
        $this->sandbox = $sandbox;
        $this->apiBaseUrl = $this->sandbox ? self::SANDBOX_URL : self::LIVE_URL;
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->proxy = $proxy;

        parent::__construct();
    }

    /*
     * https://developer.paypal.com/docs/api/payments/v1/
     * Create payment
     */
    /**
     * @throws ReportableException
     */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount'      => [
                        'currency_code' => $val['cur'],
                        'value'    => round((float)$val['amount'], 2),
                    ],
                    'custom_id' => $localPayment->id,
                    'description' => $this->getRemoteDescription($val, $localPayment),
                ]
            ],

            'application_context' => [
                'locale' => $this->buildPayPalLocale($val['locale']),
                'return_url' => $this->proxy->getEncryptedUrl($val['success_url']),
                'cancel_url' => $this->proxy->getEncryptedUrl($val['cancel_url']),
            ]
        ];

        $response = Http::withToken($this->getBearerToken())
            ->post($this->apiBaseUrl . '/v2/checkout/orders', $data)->json();

        $this->log->debug('Pay pal order is created', [
            'paymentId'        => $localPayment->id,
            'paymentForeignId' => $localPayment->foreign_id,
            'payPalId'         => $this->getRemotePaymentId($response)
        ]);

        return [
            'id' => $this->getRemotePaymentId($response),
            'url' => $this->getCheckoutUrl($response),
        ];
    }

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        return __('payment.order_payment', ['payment_id' => $localPayment->id]);
    }

    protected function getBearerToken(): string
    {
        $data = ['grant_type' => 'client_credentials'];

        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post($this->apiBaseUrl . '/v1/oauth2/token', $data)->json();

        $accessToken = data_get($response,'access_token');

        if(!$accessToken){
            throw (new ReportableException('PayPal api token get error'))
                ->withData(['exception' => json_encode($response)]);
        }

        return $accessToken;
    }

    protected function getCheckoutUrl(array $payPalPayment): string
    {
        $links = data_get($payPalPayment, 'links');

        if(empty($links)){
            throw (new ReportableException('PayPal payment links extraction fail'))
                ->withData(['exception' => json_encode($payPalPayment)]);
        }

        foreach ($links as $link) {
            if ($link['rel'] === 'approve') {
                return $this->proxy->getEncryptedUrl($link['href']);
            }
        }

        throw (new ReportableException('approval_url link is not found at pay pal api response'))
            ->withData(['exception' => json_encode($payPalPayment)]);
    }

    protected function getRemotePaymentId(array $payPalPayment): string
    {
        $id = data_get($payPalPayment, 'id');

        if(!$id) {
            throw (new ReportableException('Param id is not found at PayPal api request'))
                ->withData(['exception' => json_encode($payPalPayment)]);
        }

        return $id;
    }

    public function getForeignPaymentId($request): string
    {
        $status = data_get($this->hookRequest, 'event_type');

        if(self::REMOTE_SUCCEEDED !== $status){
            return '';
        }

        return data_get($this->hookRequest, 'resource.id');
    }

    /**
     * This method has been call at basic class, when getForeignPaymentId return not empty string
     * @param Request $request
     * @return string
     */
    public function mapRequestToStatus($request): string
    {
        return Payment::STATUS_PAYMENT_SUCCEEDED;
    }

    /*
     * https://developer.paypal.com/api/rest/reference/locale-codes/
     * https://developer.paypal.com/docs/api/payments/v2/ application_context locale
     */
    protected function buildPayPalLocale(string $locale): string
    {
        return match ($locale) {
            'de'       => 'de-DE',
            'en', 'tr' => 'en-US',
            'es'       => 'es-ES',
            'it'       => 'it-IT',
            'pt'       => 'pt-PT',
            'ru', 'uk' => 'ru-RU',

            default    => 'en-US'
        };
    }

    public function handleHook(Request $request)
    {
        $this->hookRequest = $request->all();

        //Uncomment if need to debug hooks
        //$this->log->debug('PayPal handleHook', ['r' => $this->hookRequest]);

        return parent::handleHook($request);
    }

    protected function tryUpdatePayment(Payment $payment, string $status): bool
    {
        if ($status === Payment::STATUS_PAYMENT_SUCCEEDED) {
            $this->log->debug('Execute PayPal payment ...', [
                'id' => $payment->foreign_id
            ]);

            $foreignId = $payment->foreign_id;

            $response = Http::withToken($this->getBearerToken())
                ->asJson()
                ->send('POST', $this->apiBaseUrl . "/v2/checkout/orders/${foreignId}/capture")
                ->json();

            //$this->log->debug('PayPal checkout order response', ['r' => $response]);

            if(!data_get($response, 'id')){
                throw (new ReportableException('Execute PayPal payment error'))
                    ->withData(['exception' => json_encode($response)]);
            }
        }

        return parent::tryUpdatePayment($payment, $status);
    }
}
