<?php

namespace Tests\Feature;

use App\Payment;
use App\Role\UserRole;
use App\Services\MoneyService;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class PaymentsTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;

    const URL = 'http://return_url';
    const CURRENCY = Transaction::CUR_RUB;
    const PAYMENT = Payment::TYPE_YANDEX_KASSA;

    public function setUp(): void
    {
        parent::setUp();
//        TFHelpers::runCommonSeeders();
        TFHelpers::runTestSeeders();

        $this->admin = User::factory()->create([
            'roles' => [UserRole::ROLE_ADMIN],
        ]);

        $this->user = User::factory()->create();
        $this->user->giveMoney(1000, Transaction::CUR_RUB);
    }

    public function testMakePayment()
    {
        $res = $this->withToken($this->user->api_token)
                    ->post('/api/deposit', [
                        'amount'        => 1.00,
                        'cur'           => self::CURRENCY,
                        'success_url'   => self::URL,
                        'cancel_url'    => self::URL,
                        'description'   => 'Test deposit description'
                    ])
                    ->assertStatus(200);
        $this->assertNotNull($res->json('data.id'));
        $this->assertNotNull($res->json('data.url'));

        $payment = Payment::where(
            'id', $res->json('data.id'))->first();

        $this->assertNotNull($payment);
    }

    public function testUserHasPayments()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',
            $this->user->payments);
        $this->assertEmpty($this->user->payments);
    }

    public function testChangeYooPaymentStatusToCanceled()
    {
        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertCount(1, $this->user->payments);

        $response = $this->post('/api/yk_status', [
            'type' => 'notification',
            'event' => 'payment.canceled',
            'object' => [
                'id' => $payment->foreign_id,
                'amount' => [
                    'value' => 1.00,
                    'currency' => 'RUB',
                ],
            ],
        ]);

        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_PAYMENT_CANCELED, $payment->status);
    }

    public function testChangeYooPaymentStatusToSucceeded()
    {
        $payment = Payment::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertCount(1, $this->user->payments);

        $transactionsBefore = $this->user->transactions->count();
        $money = resolve(MoneyService::class);
        $balanceBefore = $money->getUserBalance($this->user, 'RUB');

        $response = $this->post('/api/yk_status', [
            'type' => 'notification',
            'event' => 'payment.succeeded',
            'object' => [
                'id' => $payment->foreign_id,
                'amount' => [
                    'value' => 1.00,
                    'currency' => 'RUB',
                ],
            ],
        ]);

        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_PAYMENT_SUCCEEDED, $payment->status);

        $this->user->refresh();
        $this->assertCount($transactionsBefore + 1, $this->user->transactions);
        $balance = $money->getUserBalance($this->user, 'RUB');
        $this->assertEquals($balanceBefore + 1.00, $balance);
    }

    /**
     * @dataProvider amountProvider
     */
    public function testMakePaymentX($amount) {
        $res = $this->withToken($this->user->api_token)
                    ->post('/api/deposit', [
                        'amount'        => $amount,
                        'cur'           => self::CURRENCY,
                        'success_url'   => self::URL,
                        'cancel_url'    => self::URL,
                        'description'   => 'Test deposit description'
                    ],
        );
        $res->assertStatus(Response::HTTP_OK);
        $this->assertNotNull($res->json('data.id'));
        $this->assertNotNull($res->json('data.url'));
    }

    public function amountProvider(): array
    {
        return [
            [10],
            [50.6],
            [253.50],
            [1000.558]
        ];
    }
}