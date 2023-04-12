<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace Database\Factories;

use App\Payment;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        $user = User::factory()->create();

        return [
            'foreign_id' => Str::random(30),
            'payment_system' => Payment::TYPE_YANDEX_KASSA,
            'status' => Payment::STATUS_PENDING,
            'amount' => 1.00,
            'currency' => 'RUB',
            'user_id' => $user->id,
            'description' => 'Тестовая оплата через яндекс-кассу',
        ];
    }
}
