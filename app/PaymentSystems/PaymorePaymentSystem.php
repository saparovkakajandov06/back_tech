<?php

namespace App\PaymentSystems;

use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Transaction;
use Illuminate\Support\Facades\Http;

class PaymorePaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'Paymore';
    protected array $availableCurrencies = [
        Transaction::CUR_RUB,
        Transaction::CUR_USD,
        Transaction::CUR_EUR
    ];

    protected string $email;
    protected string $token;
    protected string $url;
    protected string $id;

    const REMOTE_SUCCEEDED = 'pay';
    const REMOTE_CANCEL = 'cancel';
    const REMOTE_ERROR = 'error';

    public function __construct()
    {
        $this->email = env('PAYMORE_EMAIL');
        $this->token = env('PAYMORE_TOKEN');
        $this->url = env('PAYMORE_URL');
        $this->id = env('PAYMORE_ID');

        parent::__construct();
    }

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        return __('payment.order_payment', ['payment_id' => $localPayment->id]);
    }
    /* example result from https://paymore.org/help/?shell#sozdanie-platezha : {
        "id": 12345,
        "order": {
            "amount": 10,
            "currency": "RUB",
            "description": "Test Payment"
        },
        "token": "1-62aebd0e3a-3dae1e0976-73f96a4bc1",
        "wallet": {
            "id": 1
        },
        "create_date": "2017-12-25T00:07:19+00:00",
        "update_date": "2017-12-25T00:07:19+00:00",
        "status": "init",
        "custom_parameters":{
          "email": "vasia@gmail.com",
          "order_id": "515"
        },
        "payment_url": "https://paymore.org/pay/1-62aebd0e3a-3dae1e0976-73f96a4bc1"
    } */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $custom_parameters = [
            'order_id' => $localPayment->id,
        ];
        if (array_key_exists('user_name', $val)){
            $custom_parameters['payer_id'] = $val['user_name'];
        }
        $data = [
            'order' => [
                'currency'    => $val['cur'],
                'amount'      => $val['amount'],
                'description' => $this->getRemoteDescription($val, $localPayment),
            ],
            'settings' => [
                'project_id'     => $this->id,
                'payment_method' => 'card',
                'fail_url'       => $val['cancel_url'],
                'success_url'    => $val['success_url']
            ],
            'custom_parameters' => $custom_parameters,
        ];
        $response = Http::withBasicAuth($this->email, $this->token)->post($this->url, $data)->json();
        if (!array_key_exists('status', $response) || $response['status'] !== 'init') {
            throw (new ReportableException('Remote payment creation error'))
                ->withData(['exception' => json_encode($response)]);
        }
        return [
            'id' => $this->getRemotePaymentId($response),
            'url' => $this->getCheckoutUrl($response),
        ];
    }

    protected function getCheckoutUrl(array $paymorePayment): string
    {
        return $paymorePayment['payment_url'];
    }

    protected function getRemotePaymentId(array $paymorePayment): string
    {
        return $paymorePayment['id'];
    }

    public function getDefaultResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => "ok"]);
    }
    /* example result from https://paymore.org/help/?shell#dannye-dlya-obrabotchika : {
        "notification_type":"pay",
        "id": 12345,
        "order": {
            "amount": 10,
            "currency": "RUB",
            "description": "Test Payment"
        },
        "token": "1-62aebd0e3a-3dae1e0976-73f96a4bc1",
        "wallet": {
            "id": 1,
            "amount": 8.5,
            "currency": "RUB"
        },
        "create_date": "2017-12-25T00:07:19+00:00",
        "update_date": "2017-12-25T00:08:19+00:00",
        "ip": "127.0.0.1",
        "status": "successful",
        "payment_method": {
            "type": "card",
            "account": "420000xxxxxx0000",
            "card": {
                "fingerprint": "40bd001563085fc35165329ea1ff5c5ecbdbbeef",
                "brand": "VISA",
                "country": "US",
                "bank": "JPMORGAN CHASE BANK, N.A.",
                "type": "CREDIT"
            }
        },
        "custom_parameters":{
          "email": "vasia@gmail.com",
          "order_id": "515"
        },
        "is_test": true
    } */
    public function getForeignPaymentId($request): string
    {
        return data_get($request, 'id');
    }

    public function mapRequestToStatus($request): string
    {
        $status = data_get($request, 'notification_type');
        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_ERROR,
            self::REMOTE_CANCEL => Payment::STATUS_PAYMENT_CANCELED,
            default => $status,
        };
    }
}
