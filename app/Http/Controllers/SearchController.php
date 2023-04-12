<?php

namespace App\Http\Controllers;

use App;
use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Requests\OrdersSearchRequest;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\CompositeOrdersSearchService;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    // admin search
    // link - link or login
    // cur, tag
    // statuses, date range, cost
    // username, email
    #[Endpoint('ysearch')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MANAGER')]
    #[Group('order')]
    #[Text('Поиск заказов для админов')]
    #[Param('link', false, D::URL, 'or string if login')]
    #[Param('cur', false, D::STRING)]
    #[Param('tag', false, D::STRING)]
    #[Param('statuses', false, D::STRING)]
    #[Param('date_from', false, D::DATE)]
    #[Param('date_to', false, D::DATE)]
    #[Param('cost', false, D::INT)]
    #[Param('cost_from', false, D::INT)]
    #[Param('cost_to', false, D::INT)]
    #[Param('username', false, D::STRING)]
    #[Param('email', false, D::STRING)]
    #[Param('platform', false, D::STRING)]
    #[Param('id', false, D::INT)]
    #[Param('payment_id', false, D::INT)]
    #[Param('offset', false, D::INT)]
    #[Param('limit', false, D::INT)]
    public function ySearch(OrdersSearchRequest $request, CompositeOrdersSearchService $svc): ApiResponse
    {
        $svc->setLink($request->link)
            ->setCur($request->cur)
            ->setTag($request->tag)
            ->setStatuses($request->statuses)
            ->setDateFrom($request->date_from)
            ->setDateTo($request->date_to)
            ->setCost($request->cost)
            ->setCostFrom($request->cost_from)
            ->setCostTo($request->cost_to)
            ->setUsername($request->username)
            ->setEmail($request->email)
            ->setPlatform($request->platform)
            ->setOrderId($request->id)
            ->setPaymentId($request->payment_id)
            ->setOffset($request->offset)
            ->setLimit($request->limit);

        $res = [
            'count' => $svc->getCount(), // must be before get() call
            'items' => $svc->getResult()
                ->map(function($o) {
                    return [
                        'id' => $o->id,
                        'user_id' => $o->user_id,
                        'status' => $o->status,
                        'created_at' => $o->created_at,
                        'params' => $o->params,
                        'tag' => $o->userService?->tag,
                        'name' => $o->userService?->name,
                        'platform' => $o->userService?->platform,
                        'roles' => $o->user?->roles,
                        'user_name' => $o->user?->name,
                        'user_email' => $o->user?->email,
                        'completed' => $o->getChunksCompletedSumAttribute(),
                    ];
                }),
            'first_order' => $svc->firstOrder(),
            'meta' => [
                'sql' => $svc->getSql(),
            ]
        ];

        return new ApiSuccess('Результат поиска', $res);
    }

    // user search
    // Поиск заказов текущего пользователя
    // link - link or login
    // tag, statuses, date range, cost
    #[Endpoint('c_orders')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('order')]
    #[Text('Поиск заказов для текущего юзера')]
    #[Param('link', false, D::URL, 'or string if login')]
    #[Param('tag', false, D::STRING)]
    #[Param('statuses', false, D::STRING)]
    #[Param('date_from', false, D::DATE)]
    #[Param('date_to', false, D::DATE)]
    #[Param('cost', false, D::INT)]
    #[Param('cost_from', false, D::INT)]
    #[Param('cost_to', false, D::INT)]
    #[Param('platform', false, D::STRING)]
    #[Param('offset', false, D::INT)]
    #[Param('limit', false, D::INT)]
    public function ySearchForUser(OrdersSearchRequest $request, CompositeOrdersSearchService $svc): ApiResponse
    {
        $svc->setUserId(Auth::user()->id)
            ->setTag($request->tag)
            ->setLink($request->link)
            ->setStatuses($request->statuses)
            ->setDateFrom($request->date_from)
            ->setDateTo($request->date_to)
            ->setCost($request->cost)
            ->setCostFrom($request->cost_from)
            ->setCostTo($request->cost_to)
            ->setPlatform($request->platform)
            ->setOffset($request->offset)
            ->setLimit($request->limit);

        $res = [
            'count' => $svc->getCount(), // must be before get() call
            'items' => $svc->getResult()
                ->map(function($o) {
                    return [
                        'id' => $o->id,
                        'status' => $o->status,
                        'created_at' => $o->created_at,
                        'params' => $o->params,
                        'tag' => $o->userService?->tag,
                        'name' => $o->userService?->name,
                        'platform' => $o->userService?->platform,
                        'completed' => $o->getChunksCompletedSumAttribute(),
                    ];
                }),
            'first_order' => $svc->usersFirstOrder(Auth::user()->id)
        ];

        return new ApiSuccess('Результат поиска', $res);
    }
}

// user search
//        if ($tag = strtoupper($request->tag)) {
//            $q->whereHas('userService', function($q) use ($tag) {
//                if ($tag != 'AUTO') { // exclude auto
//                    $q->where('tag', 'not like', "%AUTO%");
//                }
//                $q->where('tag', 'like', "%$tag%");
//            });
//        }

//        $regexp = "/(?<scheme>http[s]?):\/\/(?<domain>[\w\.-]+)(?<path>[^?$]+)?(?<query>[^#$]+)?[#]?(?<fragment>[^$]+)?/";
//        if (preg_match($regexp, $link, $result)) {
//            $link = $result['path'];
//        }
