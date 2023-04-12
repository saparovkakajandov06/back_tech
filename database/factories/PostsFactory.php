<?php

use App\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'heading' => $faker->title,
        'description' => null,
        'body' => $faker->text,
    ];
});
