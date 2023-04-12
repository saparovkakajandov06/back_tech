<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Task;
use App\UserJob;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    $medias = collect([
        'https://www.instagram.com/p/BxxXcwaA5EF/',
        'https://www.instagram.com/p/ByFwgy6A-6w/',
        'https://www.instagram.com/p/ByIoyNvgJEH/',
        'https://www.instagram.com/p/BySmlrJAds-/',
        'https://www.instagram.com/p/BykwFIggdsz/',
        'https://www.instagram.com/p/BztKDwzg5DP/',
        'https://www.instagram.com/p/B1bl0pYB17w/',
        'https://www.instagram.com/p/B2kIuzAAIqk/',
        'https://www.instagram.com/p/B58XDpnANfT/',
        'https://www.instagram.com/p/B7UPN-dnrnN/',
    ]);

    return [
        'params' => [
            'user_job_id' => 0,
            'link' => $medias->random(),
            'count' => rand(1, 10),
        ],
        'completed' => 0,
    ];
});
