<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Transaction;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConnectumPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    const SANDBOX_URL = 'https://api.sandbox.connectum.eu';
    const LIVE_URL = 'https://api.connectum.eu';

    protected string $name = 'Connectum';

    protected array $availableCurrencies = [
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
        Transaction::CUR_RUB,
    ];

    const REMOTE_SUCCEEDED = 'charged';
    const REMOTE_CANCELED = 'rejected';
    const REMOTE_PENDING = 'pending';

    protected bool $sandbox;
    protected string $username;
    protected string $password;
    protected string $keyFile;
    protected string $keyPassword;
    protected string $apiBaseUrl;

    protected PendingRequest $httpClient;

    public function __construct(
        bool $sandbox,
        string $username,
        string $password,
        string $keyFile,
        string $keyPassword,
    ) {
        $this->sandbox = $sandbox;
        $this->apiBaseUrl = $this->sandbox ? self::SANDBOX_URL : self::LIVE_URL;
        $this->username = $username;
        $this->password = $password;
        $this->keyFile = $keyFile;
        $this->keyPassword = $keyPassword;

        $this->httpClient = Http::withBasicAuth($this->username, $this->password)
            ->withOptions([
                'base_uri' => $this->apiBaseUrl,
                'allow_redirects' => false,
                'cert' => [$this->keyFile, $this->keyPassword],
                //'debug' => true,
            ]);

        parent::__construct();
    }

    /* example result: [
        "Success" => true,
        "ErrorCode" => "0",
        "TerminalKey" => "1234567890123DEMO",
        "Status" => "NEW",
        "PaymentId" => "900000336996",
        "OrderId" => "6185fd57f21101DQmtjml",
        "Amount" => 100,
        "PaymentURL" => "https://rest-api-test.tinkoff.ru/new/5nDLEVmb",
    ] */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $httpResp = $this->httpClient->post('orders/create', [
            'amount' => number_format($val['amount'], 2, '.', ''),
            'currency' => $val['cur'],//'EUR',//
            'merchant_order_id' => $localPayment->id,
            'client' => [
                'login' => $localPayment->user_id,
            ],
            'custom_fields' => [
                'site' => config('app.domain')
            ],
            'options' => [
                'apple_pay_enabled' => 1,
                'force3d' => 1,
                'auto_charge' => 1,
                'language' => match ($val['locale']) {
                    'pt', 'tr' => 'en',
                    default => $val['locale']
                },
                'return_url' => route('connectum_redirect', ['to' => $val['success_url'], 'cancel_url' => $val['cancel_url']]),
            ]
        ]);

        if ($httpResp->status() != 201) {
            throw new ReportableException('Order not created.');
        }

        $resp = $httpResp->json();
        $data = [];

        $data['PaymentURL'] = $httpResp->getHeader('location')[0];
        $data['PaymentId'] = $resp['orders'][0]['id'];

        if (empty($data['PaymentId'])) {
            throw new ReportableException('Order not created because of PaymentId - null, Response: '. $httpResp->body());
        }
        /*Log::channel('payments')
                ->info('test connectum payment '. $data['PaymentId']);*/

        return [
            'id'  => $this->getRemotePaymentId($data),
            'url' => $this->getCheckoutUrl($data),
        ];
    }

    public function handleHookConnectum(Request $request, $order)
    {

        try {
            $this->checkSignature($request);
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
                if (in_array($status, [Payment::STATUS_PAYMENT_SUCCEEDED, self::REMOTE_PENDING])) {
                    return redirect($request->input('to', null));
                }
                else {
                    return redirect($request->input('cancel_url', null));
                }
            }
        }
        catch (\Throwable $e) {
            Log::channel('payments')
                ->info('Error ' . describe_exception($e) . ' ' . json_encode($request) . ' ' . json_encode($order));
            return redirect($request->input('to', null));
        }

        return redirect($request->input('cancel_url', null));
    }

    /**
     * @param string $id
     * @return \stdClass
     */
    public function getOrder($id)
    {
        $httpResp = $this->httpClient->get('orders/' . $id . '?expand=secure3d');
        return $httpResp->json();
    }

    protected function getCheckoutUrl(array $connectumPayment): string
    {
        return $connectumPayment['PaymentURL'];
    }

    protected function getRemotePaymentId(array $connectumPayment): string
    {
        return $connectumPayment['PaymentId'];
    }

    public function getForeignPaymentId($request): string
    {
        return data_get($request, 'orders.0.id');
    }

    public function mapRequestToStatus($request): string
    {
        $status = data_get($request, 'orders.0.status');

        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_CANCELED  => Payment::STATUS_PAYMENT_CANCELED,
            default => $status,
        };
    }
}
