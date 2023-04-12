<?php
//php artisan test --filter StatisticsTest
//запуск теста

namespace Tests\Feature;

use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Role\UserRole;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\TF\TFHelpers;


class StatisticsTest extends TestCase
{
    use DatabaseMigrations;

    private User $moderator;
    private User $user;
    private User $userAuto;
    private Carbon $weekAgo;
    private Carbon $monthAgo;
    private Carbon $twoMonthAgo;

    public function setUp(): void
    {
        parent::setUp();
        TFHelpers::runCommonSeeders();
//        TFHelpers::runTestSeeders();

        $this->moderator = User::factory()->create([
            'roles' => [UserRole::ROLE_MODERATOR],
        ]);
        $this->user = User::factory()->create();
        $this->userAuto = User::factory()->create([
            'roles' => [UserRole::ROLE_AUTO],
        ]);

        $this->weekAgo = Carbon::parse('6 days ago');
        $this->monthAgo = Carbon::parse('25 days ago');
        $this->twoMonthAgo = Carbon::parse('2 month ago');
    }

    public function testStatisticsHasOrdersWithDifferentCreatedAtTime()
    {
        $this->createManyOrders(10, $this->weekAgo);
        $this->createManyOrders(20, $this->monthAgo);
        $this->createManyOrders(8, $this->twoMonthAgo);

        $response = $this->json('GET', '/api/statistics', [
            'api_token' => $this->moderator->api_token
        ]);

        $this->assertEquals(38, $response['data']['ordersAllCount']);
        $this->assertEquals(30, $response['data']['ordersMonth']);
        $this->assertEquals(10, $response['data']['ordersWeek']);
    }

    public function testStatisticsHasUsersWithDifferentCreatedAtTime()
    {
        User::factory()->create([
            'created_at' => $this->monthAgo,
        ]);

        User::factory()->create([
            'created_at' => $this->twoMonthAgo,
        ]);

        $response = $this->json('GET', '/api/statistics', [
            'api_token' => $this->moderator->api_token
        ]);

        $this->assertEquals(11, $response['data']['localUsersCount']);
        $this->assertEquals(10, $response['data']['localUsersMonth']);
        $this->assertEquals(9, $response['data']['localUsersWeek']);
    }

    public function testStatisticsHasOrdersWithDifferentOrderStatus()
    {
        $this->createManyOrders(10, null, "STATUS_RUNNING");
        $this->createManyOrders(10, null, "STATUS_UPDATING");
        $this->createManyOrders(10, null, "STATUS_PAUSED");
        $this->createManyOrders(8, null, "STATUS_ERROR");

        $response = $this->json('GET', '/api/statistics', [
            'api_token' => $this->moderator->api_token
        ]);

        $this->assertEquals(30, $response['data']['ordersInWork']);
        $this->assertEquals(10, $response['data']['ordersUpdating']);
        $this->assertEquals(8, $response['data']['ordersError']);
    }

    public function testStatisticsHasOrdersWithDifferentUserServices()
    {
        $this->createManyOrders(10, null,'STATUS_CREATED');
        $this->assertEquals(10, CompositeOrder::count());
//        $this->assertEquals(16, UserService::count());

        $response = $this->json('GET', '/api/statistics', [
            'api_token' => $this->moderator->api_token
        ]);

        $this->assertEquals(10, $response['data']['services'][0]['total']);
    }

    private function createManyOrders(int $count, Carbon $createdAt = null, string $status = null, int $serviceId = null)
    {
        CompositeOrder::factory($count)->create([
            'status' => $status ?? Order::STATUS_RUNNING,
            'created_at' => $createdAt ?? Carbon::parse('now'),
            'user_service_id' => $serviceId ?? 16,
        ]);
    }
}