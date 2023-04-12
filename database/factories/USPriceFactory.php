<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace Database\Factories;

use App\Transaction;
use App\USPrice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class USPriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = USPrice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tag' => 'TEST_PRICE_' . Str::random(32),
            Transaction::CUR_RUB => [
                1 => 0.45,
                1000 => 0.44,
                5000 => 0.43,
                10000 => 0.41,
                25000 => 0.42,
                50000 => 0.394,
                100000 => 0.39,
            ],
            Transaction::CUR_USD => [
                1 => 0.0149,
                1000 => 0.00999,
                5000 => 0.008,
                10000 => 0.007,
                25000 => 0.006,
                50000 => 0.006,
                100000 => 0.0055,
            ],
            Transaction::CUR_EUR => [
                1 => 0.0149,
                1000 => 0.00999,
                5000 => 0.008,
                10000 => 0.007,
                25000 => 0.006,
                50000 => 0.006,
                100000 => 0.0055,
            ],
            Transaction::CUR_TRY => null,
            Transaction::CUR_UAH => null,
        ];
    }
}
