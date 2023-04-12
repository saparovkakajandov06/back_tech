<?php

namespace Database\Seeders;

use App\Price\Category;
use Illuminate\Database\Seeder;

class PriceCategorySeeder extends Seeder
{
    private $categories = [
        'Подписчики',
        'Лайки',
        'Просмотры'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->categories as $name) {
            $category = new Category();
            $category->name = $name;
            $category->save();
        }
    }
}
