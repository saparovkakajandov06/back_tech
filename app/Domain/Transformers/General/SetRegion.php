<?php

namespace App\Domain\Transformers\General;

use App\Domain\Transformers\ITransformer;
use App\UserService;
use Illuminate\Support\Facades\Log;

class SetRegion implements ITransformer
{
    public function transform(array $params, UserService $us): array
    {
//        Log::info($params);

        $ip = request()->ip_value;
        $country = request()->country_value;
        $region = request()->region_value;
        $cur = request()->currency_value;

//        Log::info("SetRegion Transformer $ip $country $region $cur");

        return array_merge($params, [
            'ip' => $ip,
            'country' => $country,
            'region' => $region,
            'cur' => $cur,
        ]);
    }
}
