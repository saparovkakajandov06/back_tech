<?php

namespace Tests\Feature\Psp;

use App\Payment;
use App\PaymentMethod;
use App\PaymentSystems\PayToDayPaymentSystem;
use App\PremiumStatus;
use App\Transaction;
use App\User;
use Database\Seeders\PayToDaySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\TestCase;

class PayToDayPspTest extends TestCase
{
    use DatabaseMigrations;

    public User $user;

    public function setUp(): void
    {
        parent::setUp();

        (new PayToDaySeeder())->run();

        $this->user = User::factory()->create([
            'premium_status_id' => PremiumStatus::factory()->create(['cur' => Transaction::CUR_EUR])->id
        ]);

        $this->user->giveMoney(1000, Transaction::CUR_EUR);
    }

    public function testPaymentUrl()
    {
        $psp = app(PayToDayPaymentSystem::class);

        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'currency' => Transaction::CUR_USD,
            'amount' => 2,
        ]);

        /**
         * @var PayToDayPaymentSystem $psp
         * @var Payment $payment
         */

        $paymentData = $psp->createRemotePayment($payment, [
            'cur' => Transaction::CUR_RUB,
            'amount' => 160,
            'success_url' => route('main_domain'),
            'cancel_url' => route('main_domain'),
            'locale' => 'ru'
        ]);

        var_dump($paymentData['url']);

        $this->assertNotEmpty($paymentData['id']);
        $this->assertNotEmpty($paymentData['url']);
    }

    public function testPaymentRequest()
    {
        $paymentMethod = PaymentMethod::where('payment_system', '=', 'paytoday')->firstOrFail();

        $request = new Request();
        $request->query->set('payment_method_id', $paymentMethod->id);

        $psp = get_payment_system_with_default(Transaction::CUR_EUR, $request);

        $this->assertTrue($psp instanceof PayToDayPaymentSystem);
    }
}
