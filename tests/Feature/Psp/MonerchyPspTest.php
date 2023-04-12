<?php

namespace Tests\Feature\Psp;

use App\Payment;
use App\PaymentMethod;
use App\PaymentSystems\MonerchyPaymentSystem;
use App\PremiumStatus;
use App\Transaction;
use App\User;
use Database\Seeders\MonerchySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\TestCase;

class MonerchyPspTest extends TestCase
{
    use DatabaseMigrations;

    public User $user;

    public function setUp(): void
    {
        parent::setUp();

        (new MonerchySeeder())->run();

        $this->user = User::factory()->create([
            'premium_status_id' => PremiumStatus::factory()->create(['cur' => Transaction::CUR_EUR])->id
        ]);

        $this->user->giveMoney(1000, Transaction::CUR_EUR);
    }

    public function testPaymentUrl()
    {
        $psp = app(MonerchyPaymentSystem::class);

        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
            'currency' => Transaction::CUR_EUR,
            'amount' => 2
        ]);

        /**
         * @var MonerchyPaymentSystem $psp
         * @var Payment $payment
         */

        $paymentData = $psp->createRemotePayment($payment, [
            'cur' => Transaction::CUR_EUR,
            'amount' => 2,
            'success_url' => route('main_domain')
        ]);

        $this->assertNotEmpty($paymentData['id']);
        $this->assertNotEmpty($paymentData['url']);
    }

    public function testPaymentRequest()
    {
        $paymentMethod = PaymentMethod::where('payment_system', '=', 'monerchy')->firstOrFail();

        $request = new Request();
        $request->query->set('payment_method_id', $paymentMethod->id);

        $psp = get_payment_system_with_default(Transaction::CUR_EUR, $request);

        $this->assertTrue($psp instanceof MonerchyPaymentSystem);
    }
}
