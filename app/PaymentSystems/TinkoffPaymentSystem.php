<?php

namespace App\PaymentSystems;

use App\Payment;
use App\Transaction;
use GuzzleHttp\Client;

class TinkoffPaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'Tinkoff';
    protected array $availableCurrencies = [
        Transaction::CUR_RUB
    ];

    protected Client $client;

    const REMOTE_SUCCEEDED = 'CONFIRMED';
    const REMOTE_CANCELED  = 'REJECTED';

    public function __construct()
    {
        $this->client = new Client(['base_uri' => env('TINKOFF_API_URL')]);

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
        $response = $this->client->post(uri: '', options: [
            'body' => json_encode([
                'Amount'          => (int)round($val['amount'] * 100, 0),
                'Description'     => $this->getRemoteDescription($val, $localPayment),
                'FailURL'         => $val['cancel_url'],
                'OrderId'         => $localPayment->id,
                'SuccessURL'      => $val['success_url'],
                'NotificationURL' => env('TINKOFF_HOOK_URL'),
                'TerminalKey'     => env('TINKOFF_TEMRINAL'),
            ]),
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        $response = json_decode(json: $response->getBody(), associative: true);
        return [
            'id' => $this->getRemotePaymentId($response),
            'url' => $this->getCheckoutUrl($response)
        ];
    }

    protected function getCheckoutUrl(array $tinkoffPayment): string
    {
        return $tinkoffPayment['PaymentURL'];
    }

    protected function getRemotePaymentId(array $tinkoffPayment): string
    {
        return $tinkoffPayment['PaymentId'];
    }

    public function getForeignPaymentId($request): string
    {
        return data_get($request->all(), 'PaymentId');
    }

    /**
     * https://www.tinkoff.ru/kassa/develop/api/notifications/setup-response/
     * Получив уведомление, верните HTTP CODE = 200 и с телом сообщения «OK»
     * (заглавными латинскими буквами без тегов).
     */
    public function getDefaultResponse(): mixed
    {
        return response('OK', 200);
    }

    public function mapRequestToStatus($request): string
    {
        $status = data_get($request, 'Status');
        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            self::REMOTE_CANCELED => Payment::STATUS_PAYMENT_CANCELED,
            default => $status,
        };
    }
}
