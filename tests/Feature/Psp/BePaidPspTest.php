<?php

namespace Tests\Feature\Psp;

use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Payment;
use App\PaymentMethod;
use App\PaymentSystems\BePaidPaymentSystem;
use App\PremiumStatus;
use App\Transaction;
use App\User;
use App\UserService;
use Database\Seeders\BePaidSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\TestCase;

class BePaidPspTest extends TestCase
{
    use DatabaseMigrations;

    public User $user;

    public function setUp(): void
    {
        parent::setUp();

        (new BePaidSeeder())->run();

        $this->user = User::factory()->create([
            'premium_status_id' => PremiumStatus::factory()->create(['cur' => Transaction::CUR_EUR])->id
        ]);

        $this->user->giveMoney(1000, Transaction::CUR_EUR);
    }

    public function testPaymentUrl()
    {
        $psp = app(BePaidPaymentSystem::class);

        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'currency' => Transaction::CUR_EUR,
            'amount' => 2,
        ]);

        /**
         * @var BePaidPaymentSystem $psp
         * @var Payment $payment
         */

        $paymentData = $psp->createRemotePayment($payment, [
            'cur' => Transaction::CUR_UZS,
            'amount' => 160,
            'success_url' => route('main_domain'),
            'cancel_url' => route('main_domain'),
            'locale' => 'ru'
        ]);

        $this->assertNotEmpty($paymentData['id']);
        $this->assertNotEmpty($paymentData['url']);
    }

    public function testPaymentRequest()
    {
        $paymentMethod = PaymentMethod::where('payment_system', '=', 'bepaid')
            ->whereJsonContains('currencies', Transaction::CUR_EUR)
            ->firstOrFail();

        $request = new Request();
        $request->query->set('payment_method_id', $paymentMethod->id);

        $psp = get_payment_system_with_default(Transaction::CUR_EUR, $request);

        $this->assertTrue($psp instanceof BePaidPaymentSystem);
    }

    public function testHookCall()
    {
        $us = UserService::factory()->create([
            'id' => 1000 + rand(100, 1000000000)
        ]);

        $order = CompositeOrder::factory()->create([
            'user_id' => $this->user->id,
            'params' => [
                'link' => 'http://',
                'count' => 100,
                'cost' => $us->getFinalCost(100, Transaction::CUR_EUR),
                'cur' => Transaction::CUR_EUR,
            ],
            'status' => Order::STATUS_SPLIT
        ]);

        $payment = Payment::factory()->create([
            'currency' => Transaction::CUR_EUR,
            'user_id' => $this->user->id,
            'order_ids' => [
                $order->id
            ]
        ]);

        $payment->foreign_id = $payment->id;
        $payment->saveOrFail();

        $hookUrl = route('bepaid_hook', ['paymentId' => $payment->id], false);

        $response = $this->call('POST', $hookUrl, [
            'transaction' => [
                'status' => 'successful',
                'tracking_id' => $payment->id
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($response->getContent());

        $updatedPayment = Payment::where('id', $payment->id)->firstOrFail();

        $this->assertEquals(Payment::STATUS_PAYMENT_SUCCEEDED, $updatedPayment->status);
    }
}
