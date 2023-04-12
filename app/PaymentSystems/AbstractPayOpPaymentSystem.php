<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

abstract class AbstractPayOpPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'PayOp';

    protected array $availableCurrencies = [
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
        Transaction::CUR_RUB
    ];

    protected string $token;
    protected string $publicKey;
    protected string $secretKey;

    protected string $apiBaseUrl;

    protected array $hookRequest;

    //Checkout payment is created and approved by buyer.
    //https://github.com/Payop/payop-api-doc/blob/master/Invoice/getInvoice.md
    const REMOTE_ACCEPTED = 1;
    const REMOTE_FAILED = 5;

    const API_BASE_URL = 'https://payop.com';

    public function __construct(
        string $publicKey,
        string $secretKey
    ) {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;

        $this->apiBaseUrl = self::API_BASE_URL;

        parent::__construct();
    }

    protected abstract function getPaymentMethodId(?string $country = null): int;

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        return __('payment.order_payment', ['payment_id' => $localPayment->id]);
    }

    /*
     * Create payment
     */
    /**
     * @throws ReportableException
     */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $country = request()->country_value;

        $order = [
            'id'       => $localPayment->id,
            'amount'   => round((float)$val['amount'], 4),
            'currency' => $val['cur']
        ];

        $this->log->debug('Create PayOp payment ...', [
            'country'         => $country,
            'paymentMethodId' => $this->getPaymentMethodId($country),
            'order'           => $order
        ]);

        ksort($order, SORT_STRING);
        $dataSet = array_values($order);
        $dataSet[] = $this->secretKey;

        $sign = hash('sha256', implode(':', $dataSet));

        $order['items'] = [
            $order
        ];

        $order['description'] = $this->getRemoteDescription($val, $localPayment);

        $data = [
            'publicKey'     => $this->publicKey,
            'order'         => $order,
            'signature'     => $sign,
            'payer'         => [
                'email' => $localPayment->user->email ?? 'user@payop.com'
            ],
            'paymentMethod' => $this->getPaymentMethodId($country),
            'language'      => $val['locale'],

            'resultUrl' => $val['success_url'],
            'failUrl'   => $val['cancel_url']
        ];


        $response = Http::asJson()
            ->post($this->apiBaseUrl . '/v1/invoices/create', $data)->json();

        $invoiceId = data_get($response, 'data');

        if (intval(data_get($response, 'status')) !== 1 || !$invoiceId) {
            throw (new ReportableException('PayOp invoice create error'))
                ->withData(['exception' => json_encode($response)]);
        }

        $this->log->debug('PayOp invoice is created', [
            'paymentId' => $localPayment->id,
            'invoiceId' => $invoiceId
        ]);

        $checkoutUrl = sprintf(
            'https://checkout.payop.com/%s/payment/invoice-preprocessing/%s',
            $val['locale'],
            $invoiceId
        );

        return [
            'id' => $invoiceId,
            'url' =>$checkoutUrl,
        ];
    }

    protected function getCheckoutUrl(array $data): string
    {
        $checkoutUrl = data_get($data, 'checkoutUrl');

        if (empty($checkoutUrl)) {
            throw (new ReportableException('Param: [checkoutUrl] is not found at PayOp data'))
                ->withData(['exception' => json_encode($data)]);
        }

        return $checkoutUrl;
    }

    protected function getRemotePaymentId(array $data): string
    {
        $id = data_get($data, 'invoiceId');

        if (!$id) {
            throw (new ReportableException('Param: [invoiceId] is not found at PayOp data'))
                ->withData(['exception' => json_encode($data)]);
        }

        return $id;
    }

    public function getForeignPaymentId($request): string
    {
        $status = data_get($this->hookRequest, 'invoice.status');
        $status = intval($status);

        if (!in_array($status, [self::REMOTE_FAILED, self::REMOTE_ACCEPTED])) {
            return '';
        }

        return data_get($this->hookRequest, 'invoice.id');
    }

    /**
     * This method has been call at basic class, when getForeignPaymentId return not empty string
     * @param Request $request
     * @return string
     */
    public function mapRequestToStatus($request): string
    {
        $status = data_get($this->hookRequest, 'invoice.status');
        $status = intval($status);

        return match ($status) {
            self::REMOTE_ACCEPTED => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_FAILED => Payment::STATUS_PAYMENT_CANCELED,

            default => $status,
        };
    }

    public function handleHook(Request $request)
    {
        $this->hookRequest = $request->all();

        //Uncomment if need to debug hooks
        $this->log->debug('PayOp handleHook', ['r' => $this->hookRequest]);

        return parent::handleHook($request);
    }
}
