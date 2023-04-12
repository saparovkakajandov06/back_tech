<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Action;
use App\Domain\Models\Chunk;
use App\User;
use Faker\Generator as Faker;

$factory->define(Action::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id,
        'chunk_id' => factory(Chunk::class)->create()->id,
    ];
});
