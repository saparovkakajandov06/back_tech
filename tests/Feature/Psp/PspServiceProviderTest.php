<?php

namespace Tests\Feature\Psp;

use App\PaymentSystems\BasePaymentSystem;
use App\PaymentSystems\PaymentSystem;
use App\PremiumStatus;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PspServiceProviderTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        foreach (Transaction::CUR as $currency) {
            $this->user = User::factory()->create([
                'premium_status_id' => PremiumStatus::factory()->create(['cur' => $currency])->id
            ]);

            $this->user->giveMoney(1000, $currency);
        }

        //Yoo psp fix
        $_SERVER['HTTP_HOST'] = 'localhost';
    }

    public function testPspConstructors()
    {
        $paymentSystems = config('payment-systems.admin.paymentSystems');

        foreach ($paymentSystems as $key => $paymentSystem) {
            $psp = app($paymentSystem['class']);

            $this->assertInstanceOf(BasePaymentSystem::class, $psp, "Psp ${key} is not an instance of BasePaymentSystem");
            $this->assertInstanceOf(PaymentSystem::class, $psp, "Psp ${key} is not an instance of PaymentSystem interface");

            /**
             * @var BasePaymentSystem $psp
             */
            $currency = Arr::get($psp->getAvailableCurrencies(), 0);

            $this->assertNotNull($currency);

            $this->assertTrue($psp->hasCurrency($currency));
        }
    }
}
