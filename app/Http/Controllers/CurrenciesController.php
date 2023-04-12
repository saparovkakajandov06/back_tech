<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
     /**
     * @OA\Get(
     *      path="/api/cur/rate-to/{currency1}/{currency2}",
     *      operationId="usd",
     *      tags={"Currencies"},
     *      summary="Get rate to Currency",
     *      description="Получить курс одной валюты к другой",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *     )
     * Get usd Rate
     */
    #[Endpoint('cur/rate-to/{currency1}/{currency2}')]
    #[Verbs(D::GET)]
    #[Group('money')]
    #[Text('Получить курс одной валюты к другой')]
    public function rateTo(string $currency1, string $currency2, CurrencyService $svc): ApiResponse
    {
        $rate = $svc->getRate($currency1) / $svc->getRate($currency2);

        return new ApiSuccess('rateTo', ['rate' => $rate]);
    }
}
