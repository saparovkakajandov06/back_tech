<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

class MonerchyPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    const REMOTE_FAILED = 'PAYMENT_FAILED';
    const REMOTE_ACCEPTED = 'PAYMENT_ACCEPTED';
    const REMOTE_SUCCESS = 'PAYMENT_SUCCESS';

    const API_BASE_URL = 'https://sdk.monerchy.com';

    protected string $name = 'Monerchy';
    protected array $availableCurrencies = [
        Transaction::CUR_EUR,
    ];

    protected string $merchantID;
    protected string $apiKeyToken;
    protected LoggerInterface $log;

    protected array $hookRequest;

    public function __construct(
        string $merchantID,
        string $apiKeyToken,
        bool $isDebugMode
    )
    {
        $this->merchantID = $merchantID;
        $this->apiKeyToken = $apiKeyToken;
        $this->debug = $isDebugMode;

        parent::__construct();
    }

    /*
     * https://sdk.monerchy.com/docs/#tag/Payouts/operation/PayoutController_create
     * Create payment
     */
    /**
     * @throws ReportableException
     */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $description = $this->getRemoteDescription($val, $localPayment);
        $amount = (string)round((float)$val['amount'], 2);

        $data = [
            'currency' => $val['cur'],
            'amount' => $amount,
            'description' => $description,
            'callbackUrl' => route('monerchy_hook', ['paymentId' => $localPayment->id]),
            'returnUrl' => $val['success_url'],
            'items' => [
                [
                    'name' => $description,
                    'price' => $amount,
                    'quantity' => 1
                ]
            ]
        ];

        $response = Http::withBasicAuth($this->merchantID, $this->apiKeyToken)
            ->post(self::API_BASE_URL . '/transactions', $data)->json();

        $this->debugLog('Monerchy payout response', [
            'r' => $response
        ]);

        $error = data_get($response, 'error');

        if ($error) {
            throw (new ReportableException("Monerchy create transaction api return an error: [${error}]"))
                ->withData(['exception' => json_encode($response)]);
        }

        $this->debugLog('Monerchy order is created', [
            'paymentId' => $localPayment->id,
            'paymentForeignId' => $localPayment->foreign_id,
            'payPalId' => $this->getRemotePaymentId($response)
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

    /**
     * @throws ReportableException
     */
    protected function getRemotePaymentId(array $payment): string
    {
        return $this->dataGet($payment, 'payload.id', 'api request');
    }

    /**
     * @throws ReportableException
     */
    protected function getCheckoutUrl(array $payment): string
    {
        return $this->dataGet($payment, 'payload.paymentUrl', 'api request');
    }

    /**
     * @throws ReportableException
     */
    public function getForeignPaymentId($request): string
    {
        return $this->dataGet($this->hookRequest, 'transaction.id');
    }

    /*
     * https://sdk.monerchy.com/docs/#tag/Payouts/operation/payout-callback
     */

    /**
     * This method has been call at basic class, when getForeignPaymentId return not empty string
     * @param Request $request
     * @return string
     * @throws ReportableException
     */
    public function mapRequestToStatus($request): string
    {
        $status = $this->dataGet($this->hookRequest, 'transaction.status');

        return match ($status) {
            self::REMOTE_SUCCESS => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_FAILED => Payment::STATUS_PAYMENT_CANCELED,

            default => $status,
        };
    }

    public function handleHook(Request $request)
    {
        $this->hookRequest = $request->all();

        //Uncomment if need to debug hooks
        $this->debugLog('Monerchy handleHook', ['r' => $this->hookRequest]);

        return parent::handleHook($request);
    }

    protected function tryUpdatePayment(Payment $payment, string $status): bool
    {
        if (!parent::tryUpdatePayment($payment, $status)) {
            return false;
        }

        if ($status !== self::REMOTE_ACCEPTED) {
            return true;
        }

        $foreignId = $payment->foreign_id;

        $data = [
            'id' => $foreignId,
        ];

        $this->debugLog('Confirm monerchy transaction ...', $data);

        $response = Http::withBasicAuth($this->merchantID, $this->apiKeyToken)
            ->post(self::API_BASE_URL . "/transactions/${foreignId}/payment/confirm", $data)->json();

        $this->debugLog('Monerchy confirm transaction response', ['r' => $response]);

        $error = data_get($response, 'error');

        if ($error) {
            throw (new ReportableException("Monerchy confirm transaction api return an error: [${error}]"))
                ->withData(['exception' => json_encode($response)]);
        }

        $this->dataGet($response, 'payload.id', 'transaction confirm response');

        return true;
    }
}
