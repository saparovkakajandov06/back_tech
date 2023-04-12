<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Param;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    #[Endpoint('region')]
    #[Verbs(D::GET)]
    #[Group('other')]
    #[Text('Список всех Регионов')]
    #[Param('ip_value', false, D::STRING, 'ip адрес')]
    #[Param('country_value', false, D::STRING, 'страна')]
    #[Param('region_value', false, D::STRING, 'регион')]
    #[Param('currency_value', false, D::STRING, 'валюта')]
    public function region(Request $request): ApiResponse
    {
        return new ApiSuccess('User region', [
            'ip' => $request->ip_value,
            'country' => $request->country_value,
            'region' => $request->region_value,
            'currency' => $request->currency_value,
        ]);
    }
}
