<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace Database\Factories;

use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompositeOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompositeOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $us = UserService::factory()->create([
            'id' => 1000 + rand(100, 1000000000)
        ]);

        return [
            'user_id' => $user->id,
            'user_service_id' => $us->id,
            'params' => [
                'link' => 'http://',
                'count' => 100,
                'cost' => $us->getFinalCost(100, Transaction::CUR_RUB),
                'cur' => Transaction::CUR_RUB,
            ],
            'status' => Order::STATUS_CREATED,
//            'done' => false,
        ];
    }
}
