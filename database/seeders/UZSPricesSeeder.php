<?php

namespace Database\Seeders;

use App\USPrice;
use Illuminate\Database\Seeder;

class UZSPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prices = [
            'INSTAGRAM_LIKES_LIGHT4' => [
                1 => 5320 / 100,
                500 => 21280,
                1000 => 26600,
                2500 => 86640,
                5000 => 133000,
                10000 => 326800,
                25000 => 665000,
                50000 => 1504800,
                100000 => 2698000
            ],
            'INSTAGRAM_SUBS_LIGHT4' => [
                1 => 114,
                500 => 42560,
                1000 => 57000,
                2500 => 199120,
                5000 => 285000,
                10000 => 737200,
                25000 => 1425000,
                50000 => 3420000,
                100000 => 5928000
            ],
            'INSTAGRAM_VIEWS_VIDEO_LIGHT4' => [
                1 => 2280 / 100,
                500 => 9120,
                1000 => 11400,
                2500 => 37240,
                5000 => 57000,
                10000 => 136800,
                25000 => 285000,
                50000 => 646000,
                100000 => 988000
            ],
            'TIKTOK_LIKES_LIGHT4' => [
                1 => 5320 / 100,
                500 => 21280,
                1000 => 26600,
                2500 => 86640,
                5000 => 133000,
                10000 => 326800,
                25000 => 665000,
                50000 => 1504800,
                100000 => 2698000
            ],
            'TIKTOK_SUBS_LIGHT4' => [
                1 => 114,
                500 => 42560,
                1000 => 57000,
                2500 => 199120,
                5000 => 285000,
                10000 => 737200,
                25000 => 1425000,
                50000 => 3420000,
                100000 => 5928000
            ],
            'TIKTOK_VIEWS_LIGHT4' => [
                1 => 2280 / 100,
                500 => 9120,
                1000 => 11400,
                2500 => 37240,
                5000 => 57000,
                10000 => 136800,
                25000 => 285000,
                50000 => 646000,
                100000 => 988000
            ]
        ];

        foreach ($prices as $tag => $tagPrices) {
            $values = [];

            foreach ($tagPrices as $count => $tagPrice) {
                $values[$count] = $tagPrice / $count;
            }

            $model = USPrice::where('tag', '=', $tag)->firstOrFail();
            $model->UZS = $values;
            $model->saveOrFail();
        }
    }
}
