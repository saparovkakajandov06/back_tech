<?php
//php artisan test --filter ModRunTest
//запуск теста

namespace Tests\Feature;

use App\Domain\Models\CompositeOrder;
use App\Exceptions\Reportable\ModeratorActionException;
use App\Order;
use App\Role\UserRole;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class UpdatingStateTest extends TestCase
{
    use DatabaseMigrations;

    private User $moderator;
    private User $user;
    private CompositeOrder $runningOrder;

    public function setUp(): void
    {
        parent::setUp();
//        TFHelpers::runCommonSeeders();
        TFHelpers::runTestSeeders();

        $this->moderator = User::factory()->create([
            'roles' => [UserRole::ROLE_MODERATOR],
        ]);

        $this->user = User::factory()->create();

        $this->runningOrder = CompositeOrder::factory()->create([
            'status' => Order::STATUS_RUNNING,
        ]);
    }

    public function testModRunChangesUpdatingStateToRunningState()
    {
        $this->runningOrder->startUpdate();
        $this->assertEquals(Order::STATUS_UPDATING, $this->runningOrder->status);
        $this->runningOrder->update([
            'updated_at' => Carbon::parse("601 seconds ago")
        ]);
        $this->runningOrder->refresh();

        $this->runningOrder->modRun();

        $this->assertEquals(Order::STATUS_RUNNING,
            $this->runningOrder->status);
    }

    public function testModRunCantChangesUpdatingStateToRunningStateByUpdatingTimeValidator()
    {
        $this->expectException(\Exception::class);

        $order = $this->runningOrder;

        $order->startUpdate();
        $this->assertEquals(Order::STATUS_UPDATING, $order->status);

        $order->update(['updated_at' => Carbon::parse("598 seconds ago")]);
        $order->refresh();

        $order->modRun();

        $this->assertEquals(Order::STATUS_UPDATING, $order->status);
    }

    public function testModeratorChangesUpdatingStateToRunning()
    {
        $order = $this->runningOrder;
        $order->startUpdate();
        $this->assertEquals(Order::STATUS_UPDATING, $order->status);

        $order->update(['updated_at' => Carbon::parse("601 seconds ago")]);

        $this->post('/api/c_orders/mod_run', [
            'api_token' => $this->moderator->api_token,
            'orders' => $order->id,
        ])->json();
        $order->refresh();

        $this->assertEquals(Order::STATUS_RUNNING, $order->status);
    }

    public function testModeratorCantChangesUpdatingStateToRunningByUpdatingTimeValidator()
    {
        $order = $this->runningOrder;
        $order->startUpdate();
        $this->assertEquals(Order::STATUS_UPDATING, $order->status);

        $order->update(['updated_at' => Carbon::parse("597 seconds ago")]);
        $this->post('/api/c_orders/mod_run', [
            'api_token' => $this->moderator->api_token,
            'orders' => $order->id,
        ])->assertJson([
            'status' => 'error',
            'error' => ModeratorActionException::class,
            'message' => __('exceptions.please_wait'),
        ]);

        $order->refresh();

        $this->assertEquals(Order::STATUS_UPDATING, $order->status);
    }
    public function testUserCantChangesUpdatingStateToRunning()
    {
        $order = $this->runningOrder;
        $order->startUpdate();
        $this->assertEquals(Order::STATUS_UPDATING, $order->status);

        $order->update(['updated_at' => Carbon::parse("601 seconds ago")]);

        $this->post('/api/c_orders/mod_run', [
            'api_token' => $this->user->api_token,
            'orders' => $order->id,
        ])->assertJson([
            'status' => 'error',
            'message' =>'Unauthenticated.',
        ]);

        $this->assertEquals(Order::STATUS_UPDATING, $order->status);
    }
}