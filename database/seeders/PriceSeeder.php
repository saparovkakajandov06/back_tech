<?php

namespace Database\Seeders;

use App\Price\Category;
use App\Price\Feature;
use App\Price\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    private $prices = [
        [
            'cost' => 5,
            'count' => 100,
            'is_featured' => false,
            'economy' => 100,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();
        $features = Feature::all()->pluck('id')->toArray();

        foreach($this->prices as $price) {
            $price['category_id'] = $categories->random()->id;
            $price['features'] = $features;

            Price::create($price);
        }
    }
}
