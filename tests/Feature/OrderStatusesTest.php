<?php

namespace Tests\Feature;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Role\UserRole;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class OrderStatusesTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;

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

    public function testOrderShouldChangeStatus()
    {
        $order = CompositeOrder::factory()->create();
        $this->assertEquals(Order::STATUS_CREATED, $order->status);
        $order->refresh();

        $order->state()->split();

        $this->assertEquals(Order::STATUS_SPLIT, $order->status);
    }

    public function testCancelOrder()
    {
        $order = CompositeOrder::factory()->create([
            'status' => Order::STATUS_RUNNING
        ]);

        $tCount = Transaction::count();

        $order->startUpdate()->cancel();

        $this->assertDatabaseCount('transactions', $tCount + 1);

        $t = Transaction::whereType(Transaction::INFLOW_REFUND)->first();
        $this->assertEquals(0.45 * 100, $t->amount);
    }

    public function testPartFunction()
    {
        $chunk = Chunk::factory()->create();
        $order = $chunk->compositeOrder;

        $this->assertEquals(0, $order->getCompletedPart());

        $chunk->update(['completed' => 30]);

        $order->refresh();
        $this->assertEquals(0.30, $order->getCompletedPart());
    }

    public function testPartialCompletionEvent()
    {
        $chunk = Chunk::factory()->create();
        $order = $chunk->compositeOrder;
        $chunk->update(['completed' => 70]);
        $order->update(['status' => Order::STATUS_RUNNING]);

        // order cost == 45 rub

        $order->startUpdate()->partial();

        $t = Transaction::whereType(Transaction::INFLOW_REFUND)->first();
        $this->assertEquals(0.45 * 0.30 * 100, $t->amount);
    }
}