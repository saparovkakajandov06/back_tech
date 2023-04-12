<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BePaidPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    const REMOTE_FAILED = 'failed';
    const REMOTE_INCOMPLETE = 'incomplete';
    const REMOTE_SUCCESS = 'successful';
    const REMOTE_EXPIRED = 'expired';

    const CHECKOUT_BASE_URL = 'https://checkout.bepaid.tech';

    protected string $name = 'BePaid';
    protected array $availableCurrencies = [
        Transaction::CUR_RUB,
        Transaction::CUR_EUR,
        Transaction::CUR_USD,
        Transaction::CUR_UZS,
    ];

    protected int $shopId;
    protected string $shopSecret;

    protected array $hookRequest;

    protected bool $isTestMode = true;

    protected array $availableLanguages = ['en', 'es', 'tr', 'de', 'it', 'ru', 'zh', 'fr', 'da', 'sv', 'no', 'fi', 'pl', 'ja', 'be', 'uk', 'ka', 'ro'];

    public function __construct(
        int    $shopId,
        string $shopSecret,
        bool   $isTestMode,
        bool   $isDebugMode,
    )
    {
        $this->shopId = $shopId;
        $this->shopSecret = $shopSecret;
        $this->isTestMode = $isTestMode;
        $this->debug = $isDebugMode;

        parent::__construct();
    }

    /*
     * https://docs.bepaid.tech/ru/products
     * Create payment
     */
    /**
     * @throws ReportableException
     */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $description = $this->getRemoteDescription($val, $localPayment);
        $amount = round((float)$val['amount'], 2);
        $amount *= 100;

        $lang = $this->buildLanguage($val['locale']);

        $data = [
            'checkout' => [
                'transaction_type' => 'payment',
                'duplicate_check' => false,
                'test' => $this->isTestMode,

                'settings' => [
                    'language' => $lang,

                    'success_url' => $val['success_url'],
                    'fail_url' => $val['cancel_url'],
                    'decline_url' => $val['cancel_url'],
                    'cancel_url' => $val['cancel_url'],
                    'notification_url' => route('bepaid_hook', ['paymentId' => $localPayment->id]),
                ],

                'order' => [
                    'tracking_id' => $localPayment->id,
                    'description' => $description,
                    'currency' => $val['cur'],
                    'amount' => $amount,
                ],

                'customer' => [
                    'email' => $localPayment->user->email ?? 'info@smmtouch.com',
                    'first_name' => $localPayment->user->name ?? ''
                ]
            ]
        ];
        $this->debugLog('Send BePaid checkout response ...', [
            'data' => $data,
            'val' => $val
        ]);

        $response = Http::withBasicAuth($this->shopId, $this->shopSecret)
            ->post(self::CHECKOUT_BASE_URL . '/ctp/api/checkouts', $data)->json();

        $this->debugLog('BePaid payout checkout', [
            'r' => $response
        ]);

        $error = data_get($response, 'message');

        if ($error) {
            throw (new ReportableException("BePaid create transaction api return an error: [${error}]"))
                ->withData(['exception' => json_encode($response)]);
        }

        $this->debugLog('BePaid order is created', [
            'paymentId' => $localPayment->id,
            'token' => $this->dataGet($response, 'checkout.token', 'api request'),
        ]);

        return [
            'id' => $localPayment->id,
            'url' => $this->getRemotePaymentUrl($response)
        ];
    }

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        return __('payment.order_payment', ['payment_id' => $localPayment->id]);
    }

    public function buildLanguage(string $lang): string
    {
        $lang = strtolower($lang);

        if (in_array($lang, $this->availableLanguages)) {
            return $lang;
        }

        return 'en';
    }

    /**
     * @throws ReportableException
     */
    protected function getRemotePaymentUrl(array $payment): string
    {
        return $this->dataGet($payment, 'checkout.redirect_url', 'api request');
    }

    /**
     * This method has been call at basic class, when getForeignPaymentId return not empty string
     * @param Request $request
     * @return string
     * @throws ReportableException
     */
    public function mapRequestToStatus($request): string
    {
        $status = $this->getForeignStatus();

        return match ($status) {
            self::REMOTE_SUCCESS => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_FAILED, self::REMOTE_EXPIRED, self::REMOTE_INCOMPLETE => Payment::STATUS_PAYMENT_CANCELED,

            default => $status,
        };
    }

    public function getForeignStatus(): string
    {
        return $this->dataGet($this->hookRequest, 'transaction.status');
    }

    public function handleHook(Request $request)
    {
        $this->hookRequest = $request->all();

        $this->debugLog('BePaid handleHook', ['r' => $this->hookRequest]);

        $this->debugLog('Hook data', [
            'id' => $this->getForeignPaymentId($request),
            'status' => $this->getForeignStatus()
        ]);

        return parent::handleHook($request);
    }

    /**
     * @throws ReportableException
     */
    public function getForeignPaymentId($request): string
    {
        return $this->dataGet($this->hookRequest, 'transaction.tracking_id');
    }
}
