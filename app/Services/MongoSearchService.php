<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class MongoSearchService
 * @package App\Services
 * @deprecated
 */
class MongoSearchService
{
    public function __construct()
    {
    }

    public function updateTimestamp()
    {
        DB::connection('mongodb')->collection('update')->truncate();
        DB::connection('mongodb')->collection('update')->insert([
            'time' => Carbon::now()->toDateTimeString('microsecond')
        ]);

    }

    public function getTimestamp()
    {
        return DB::connection('mongodb')->collection('update')->first()['time'];
    }

    public function clearCache()
    {
        DB::connection('mongodb')->collection('search')->truncate();
    }

    public function insertCache($array)
    {
        DB::connection('mongodb')->collection('search')->insert($array);
    }

    public function getRecords()
    {
        return DB::connection('mongodb')->collection('search')->count();
    }
}
