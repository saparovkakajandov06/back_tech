<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Exceptions\NonReportable\NonReportableException;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\USPrice;
use Illuminate\Http\Request;

class USPricesController extends Controller
{

    #[Endpoint('user_services/{tag}/prices')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('money')]
    #[Text('Цены для сервиса')]
    #[Param('tag', true, D::STRING, 'user service tag', 'INSTAGRAM_LIKES_LK')]
    public function byTag(Request $request, $tag): ApiResponse
    {
        $p = USPrice::where('tag', $tag)->first();
        if (! $p) {
            throw new NonReportableException("user service not found: $tag");
        }

        return new ApiSuccess('Ok', $p);
    }
    #[Endpoint('user_services/{tag}/prices')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MODERATOR')]
    #[Group('money')]
    #[Text('Обновление цен пользовательского сервиса')]
    #[Param('tag', true, D::STRING, 'user service tag', 'INSTAGRAM_LIKES_LK')]
    #[Param('cur', true, D::STRING, 'user service price', 'USD')]
    public function update(Request $request, $tag): ApiResponse
    {
        $p = USPrice::where('tag', $tag)->firstOrFail();
        $p->update($request->all());

        return new ApiSuccess('Updated', $p);
    }
}
