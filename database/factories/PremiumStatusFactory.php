<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace Database\Factories;

use App\Domain\Models\Labels;
use App\PremiumStatus;
use App\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class PremiumStatusFactory extends Factory
{
    protected $model = PremiumStatus::class;

    public function definition()
    {
        return [
            'name' => 'LEVEL_1',
            'online_support' => 1,
            'personal_manager' => 0,
            'discount' => [
                Labels::DISCOUNT_BASIC => 0,
                Labels::DISCOUNT_LIKES => 0,
                Labels::DISCOUNT_VIEWS => 0,
                Labels::DISCOUNT_SUBS => 0,
                Labels::DISCOUNT_COMMENTS => 0,
                Labels::DISCOUNT_AUTO_LIKES => 0,
                Labels::DISCOUNT_AUTO_VIEWS => 0
            ],
            'cash' => 0,
            'cur' => Transaction::CUR_RUB
        ];
    }
}
