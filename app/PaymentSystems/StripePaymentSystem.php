<?php

namespace App\PaymentSystems;

use App\Payment;
use App\Transaction;
use Illuminate\Http\Request;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Class StripePaymentSystem
 * @package App\PaymentSystems
 * @deprecated use \App\PaymentSystems\StripeRemotePaymentSystem::class instead
 *
 * Интеграция платежной системы Stripe
 */
class StripePaymentSystem extends BasePaymentSystem implements PaymentSystem
{
    protected string $name = 'Stripe';

    protected array $availableCurrencies = [
        Transaction::CUR_USD,
        Transaction::CUR_EUR,
    ];

    const REMOTE_SUCCEEDED = 'charge.succeeded';

    protected string $secret;
    protected string $hookSecret;
    protected PaymentSystemProxy $proxy;

    protected Event $event;

    public function __construct(
        string $secret,
        string $hookSecret,
        PaymentSystemProxy $proxy,
    ) {
        $this->secret = $secret;
        $this->hookSecret = $hookSecret;
        $this->proxy = $proxy;

        parent::__construct();
    }

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        return __('payment.order_payment', ['payment_id' => $localPayment->id]);
    }

    public function createRemotePayment(Payment $localPayment, array $val): array
    {
        $client = new StripeClient($this->secret);
        $session = $client->checkout->sessions->create([
            'cancel_url' => $this->proxy->getEncryptedUrl($val['cancel_url']),
            'mode' => 'payment',
            'success_url' => $this->proxy->getEncryptedUrl($val['success_url']),
            'client_reference_id' => $localPayment->id,
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
            'payment_method_types' => ['card'],
        ]);
        return [
            'url' => $this->proxy->getEncryptedUrl($session->url),
            'id' => $session->payment_intent,
            'payment_intent' => $session->payment_intent
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

    protected function getEventData(Request $request): Event
    {
        $payload = $request->getContent();
        $sig_header = $request->header('stripe_signature', '');
        return Webhook::constructEvent($payload, $sig_header, $this->hookSecret);
    }

    public function getForeignPaymentId($request): string
    {
        return match ($this->event->data->object->object) {
            'charge',
            'checkout.session' => $this->event->data->object->payment_intent,
            'payment_intent'   => $this->event->data->object->id,
            default => ''
        };
    }

    public function mapRequestToStatus($request): string
    {
        $this->event = $this->getEventData($request);
        $status = $this->event->type;
        return match ($status) {
            self::REMOTE_SUCCEEDED => Payment::STATUS_PAYMENT_SUCCEEDED,
            default => $status,
        };
    }
}
