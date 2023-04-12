<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Services\CurrencyService;
use App\Transaction;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayToDayPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    const LIVE_URL = 'https://shopozz.ru/api/';
    const REMOTE_SUCCEEDED = "1";
    const REMOTE_CANCELED = "0";
    protected string $name = 'PayToDay';
    protected array $availableCurrencies = [
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
        Transaction::CUR_RUB,
    ];
    protected string $apiBaseUrl;
    protected string $shop_id;
    protected string $api_key;

    protected PendingRequest $httpClient;

    public function __construct(
        int    $shop_id,
        string $api_key
    )
    {
        $this->apiBaseUrl = self::LIVE_URL;
        $this->shop_id = $shop_id;
        $this->api_key = $api_key;

        $this->httpClient = Http::withHeaders([
            'User-Agent' => '',
        ])->withOptions([
            'base_uri' => $this->apiBaseUrl,
        ]);

        parent::__construct();
    }

    /* example {
        "invoice_id":1,
        "payment_link":"https:\/\/knowperfectly.com\/ru\/payment_form\/smmtouch?invoiceID=1&code=ffcc3b250e8be477xxxxxe8281d7a43c3"
    }*/
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $currencyService = resolve(CurrencyService::class);
        $rubAmount = $currencyService->convert($val['cur'], 'RUB', $val['amount']);

        $httpResp = $this->httpClient->get('create_payment', [
            'api_key' => $this->api_key,
            'order_id' => $localPayment->id,
            'order_sum_rub' => $rubAmount,
            'order_description' => $this->getRemoteDescription($val, $localPayment),
            'client' => [
                'name' => $localPayment->user->name ?? $localPayment->user_id,
                'email' => $localPayment->user->email ?? 'info@smmtouch.com',
            ],
            'language' => match ($val['locale']) {
                'ru' => 'ru',
                default => 'en'
            },
        ]);

        if ($httpResp->status() != 200) {
            throw new ReportableException('Order is not created.');
        }

        $resp = $httpResp->json();
        return [
            'id' => $resp['invoice_id'],
            'url' => $resp['payment_link']
        ];
    }

    public function handleUpdatePayToDate(Request $request, $order)
    {
        try {
            $status = $this->mapRequestToStatus($order);

            if ($paymentForeignId = $this->getForeignPaymentId($order)) {

                $payment = Payment::where('foreign_id', $paymentForeignId)->firstOrFail();
                /**
                 * TODO: rewrite to Payment model method sometime in the future
                 */

                if (!$this->tryUpdatePayment($payment, $status)) {
                    Log::channel('payments')
                        ->info('Request ' . json_encode($order));
                }
            }

        } catch (\Throwable $e) {
            Log::channel('payments')
                ->info('Error ' . json_encode($e) . ' ' . json_encode($request) . ' ' . json_encode($order));
        }
    }

    /**
     * @throws ReportableException
     */
    public function mapRequestToStatus($request): string
    {
        $status = $this->dataGet($request, 'transactions.0.status');

        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_CANCELED => Payment::STATUS_PAYMENT_CANCELED,
            default => $status,
        };
    }

    /**
     * @throws ReportableException
     */
    public function getForeignPaymentId($request): string
    {
        return $this->dataGet($request, 'transactions.0.invoice_id');
    }

    /**
     * @param string $id
     * @return \stdClass
     */
    public function getOrder($id)
    {
        $httpResp = $this->httpClient->get('get_orders', [
            'api_key' => $this->api_key,
            'user_id' => $this->shop_id,
            'invoice_ids' => $id
        ]);

        if ($httpResp->body() == 'false')
            return false;

        return $httpResp->json();
    }

    protected function getCheckoutUrl(array $payToDayPayment): string
    {
        return $payToDayPayment['PaymentURL'];
    }

    protected function getRemotePaymentId(array $payToDayPayment): string
    {
        return $payToDayPayment['PaymentId'];
    }
}
