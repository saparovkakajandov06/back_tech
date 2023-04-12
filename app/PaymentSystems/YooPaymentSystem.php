<?php

namespace App\PaymentSystems;

use App\Payment;
use App\Transaction;
use YooKassa\Client;
use YooKassa\Model\NotificationEventType;

class YooPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'YooKassa';
    protected array $availableCurrencies = [
        Transaction::CUR_RUB
    ];

    protected array $preselectedPaymentMethods = [
        'QW'
    ];

    protected string $contractUrlTpl = 'https://yoomoney.ru/checkout/payments/v2/contract/%s?orderId=%s';
    protected string $preselectedUrlTpl = 'https://yoomoney.ru/payments/external/confirmation?preselectedPaymentType=%s&orderId=%s';

    public const USE_FOR_APP = true;
    public const ICON_FOR_APP = 'yookassa/d3e8869fb97ed439159c1f5ab486ae26.svg';

    protected Client $yooClient;

    protected ?string $paymentMethodId = null;

    public function __construct()
    {
        $this->yooClient = new Client();
        if ($_SERVER['HTTP_HOST'] === 'nakrutka.shop') {
            $this->yooClient->setAuth(
                env('YK_SHOP_NAKRUTKA_ID'),
                env('YK_SHOP_NAKRUTKA_SECRET'),
            );
        } else {
            $this->yooClient->setAuth(
                env('YK_SHOP_ID'),
                env('YK_SHOP_SECRET'),
            );
        }

        parent::__construct();
    }

    public function setPaymentMethod(string $methodId)
    {
        $this->paymentMethodId = $methodId;
    }

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        // return substr($val['description'], 0, 128);
        return substr(__('payment.order_payment', ['payment_id' => $localPayment->id]), 0, 128);
    }

    /* example result: {
        "id":"29192065-000f-5000-8000-1d7e30f84291",
        "status":"pending",
        "recipient":{
            "account_id":"596296",
            "gateway_id":"1567280"
        },
        "amount":{
            "value":"1.00",
            "currency":"RUB"
        },
        "description":"description",
        "created_at":"2021-11-06T23:15:49+00:00",
        "confirmation":{
            "enforce":false,
            "confirmation_url":"https:\/\/yoomoney.ru\/checkout\/payments\/v2\/contract?orderId=29192065-000f-5000-8000-1d7e30f84291",
            "type":"redirect"
        },
        "paid":false,
        "refundable":false,
        "transfers":[],
        "test":true
    } */
    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $yooPayment = $this->yooClient->createPayment(
            [
                'amount'       => [
                    'value'    => $val['amount'],
                    'currency' => 'RUB',
                ],
                'capture'      => true,
                'confirmation' => [
                    'type'       => 'redirect',
                    'return_url' => $val['success_url'],
                ],

                'description' => $this->getRemoteDescription($val, $localPayment),
                'metadata'    => [
                    'payment_id' => $localPayment->id
                ],
            ],
            uniqid('', true)
        );

        return [
            'id'  => $this->getRemotePaymentId($yooPayment),
            'url' => $this->getCheckoutUrl($yooPayment)
        ];
    }

    protected function getCheckoutUrl($yooPayment): string
    {
        $url = $yooPayment->getConfirmation()->getConfirmationUrl();

        if(!$this->paymentMethodId){
            return $url;
        }

        $query = parse_url($url, PHP_URL_QUERY);
        $params = [];
        parse_str($query, $params);

        $orderId = $params['orderId'];

        if(in_array($this->paymentMethodId, $this->preselectedPaymentMethods)){
            return sprintf($this->preselectedUrlTpl, $this->paymentMethodId, $orderId);
        }

        return sprintf($this->contractUrlTpl, $this->paymentMethodId, $orderId);
    }

    protected function getRemotePaymentId($yooPayment): string
    {
        return $yooPayment->getId();
    }

    public function getForeignPaymentId($request): string
    {
        return data_get($request->all(), 'object.id');
    }

    /**
     * https://github.com/yoomoney/yookassa-sdk-php/blob/master/docs/examples/01-configuration.md#использование
     * Вам нужно подтвердить, что вы получили уведомление. Для этого ответьте HTTP-кодом 200.
     * ЮKassa проигнорирует всё, что будет находиться в теле или заголовках ответа.
     * Ответы с любыми другими HTTP-кодами будут считаться невалидными, и ЮKassa продолжит
     * доставлять уведомление в течение 24 часов, начиная с момента, когда событие произошло.
     */

    public function mapRequestToStatus($request): string
    {
        $status = data_get($request, 'event');
        return match ($status) {
            NotificationEventType::PAYMENT_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            NotificationEventType::PAYMENT_CANCELED => Payment::STATUS_PAYMENT_CANCELED,
            default => $status,
        };
    }
}
