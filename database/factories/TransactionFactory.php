<?php

namespace Database\Factories;

use App\Transaction;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'type' => function() {
                $types = [
                    Transaction::INFLOW_REF_BONUS,
                    Transaction::OUTFLOW_WITHDRAWAL,
                    Transaction::INFLOW_TEST,
                    Transaction::INFLOW_CREATE,
                    Transaction::OUTFLOW_TEST,
                ];
                return collect($types)->random();
            },
            'amount' => random_int(0, 100) + random_int(0, 100) / 100,
            'event_id' => uniqid(),
        ];
    }
}
