<?php

namespace Database\Seeders;

use App\Price\Feature;
use Illuminate\Database\Seeder;

class PriceFeatureSeeder extends Seeder
{
    private $features = [
        'Высокое качество',
        'Мгновенный запуск',
        'С аватарками',
        'Нет бана',
        'До 150 тыс. в сутки',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->features as $name) {
            $feature = new Feature();
            $feature->name = $name;
            $feature->save();
        }
    }
}
