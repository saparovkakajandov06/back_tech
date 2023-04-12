<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TF\Gena;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $g = new Gena();

        return [
            'name' => $g->login(),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('secret'),
            'remember_token' => Str::random(10),
            'api_token' => User::getFreeToken(),
            'token_updated_at' => Carbon::now(),
            'ref_code' => Str::random(8),
            'premium_status_id' => 1,
            'lang' => 'ru',
            'cur' => 'RUB',
        ];
    }
}
