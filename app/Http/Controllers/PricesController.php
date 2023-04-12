<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Requests\PriceCreateRequest;
use App\Price\Price;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Transaction;
use App\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PricesController extends Controller
{
    #[Endpoint('prices')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('money')]
    #[Text('Получить список расценок')]
    public function index(): array
    {
        return Price::all();
    }

    #[Endpoint('prices')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('money')]
    #[Text('Добавить новую расценку')]
    #[Param('category_id', true, D::INT)]
    #[Param('cost', true, D::INT)]
    #[Param('count', true, D::INT)]
    #[Param('economy', true, D::INT)]
    #[Param('is_featured', true, D::BOOLEAN)]
    public function store(PriceCreateRequest $request): Price
    {
        $price = Price::create($request->all());

        return $price;
    }

    #[Endpoint('prices/{id}')]
    #[Verbs(D::PUT)]
    #[Role('ROLE_ANY')]
    #[Group('money')]
    #[Text('Редактировать расценку')]
    #[Param('id', true, D::INT)]
    #[Param('category_id', true, D::INT)]
    #[Param('cost', true, D::INT)]
    #[Param('count', true, D::INT)]
    #[Param('economy', true, D::INT)]
    #[Param('is_featured', true, D::BOOLEAN)]
    public function update(PriceCreateRequest $request, Price $price): Price
    {
        $price->fillOut($request->all());

        return $price;
    }

    #[Endpoint('prices/{id}')]
    #[Verbs(D::DELETE)]
    #[Role('ROLE_ANY')]
    #[Group('money')]
    #[Text('Удалить расценку')]
    #[Param('id', true, D::INT)]
    public function destroy(Price $price)
    {
        $price->delete();

        return new JsonResponse([
            'success' => 'It is deleted.'
        ]);
    }

    #[Group('money')]
    #[Endpoint('prices/costs')]
    #[Verbs(D::POST)]
    #[Text('Получить список расценок с учетом скидок')]
    #[Param('cur', false, D::STRING, 'валюта, обязательна для неавторизованного пользователя', 'RUB')]
    #[Param('items', true, D::OTHER,
      'массив объектов. каждый объект содержит tag и count',
      "items: [{}, {}, {}]")]
    public function costs(Request $request): ApiResponse
    {
        $val = $request->validate([
          'cur'   => 'string',
          'items' => 'required|array|min:1',
        ]);

        $cur = $request->input('cur');
        $user = null;
        if (! $cur) {
            $user = maybe_user();
            $cur = $user?->cur;
        }
        if (! $cur) {
            return new ApiError('no currency');
        }

        if (! in_array($cur, [
            Transaction::CUR_RUB,
            Transaction::CUR_USD,
            Transaction::CUR_EUR,
            Transaction::CUR_UZS
        ])) {
            return new ApiError('bad currency');
        }

        $items = collect($val['items'])->map(function($item) use ($cur, $user) {
            try {
                if (!is_array($item)) {
                    return $item = ['error' => 'missformed item'];
                }
                if (!array_key_exists('tag', $item)) {
                    return $item['error'] = 'no tag provided';
                }
                if (!array_key_exists('count', $item)) {
                    return $item['error'] = 'no count provided';
                }
                if ($item['count'] < 0) {
                    return $item['error'] = 'bad count';
                }
                if (!$us = UserService::findByTagCached($item['tag'])) {
                    return $item['error'] = 'no such tag';
                }
            }
            finally {
                if (array_key_exists('error', $item)) {
                    $item['cost'] = $item['discount'] = null;
                    return $item;
                }
            }
            $cost = $us->getFinalCostAndDiscount($item['count'], $cur, $user);
            [ $item['cost'], $item['discount'] ] = $cost;
            return $item;
        });

        return new ApiSuccess('Ok', [
          'cur' => $cur,
          'items' => $items,
        ]);
    }

    #[Group('money')]
    #[Endpoint('prices/tiny')]
    #[Verbs(D::GET)]
    #[Text('Получить список расценок с учетом скидок')]
    #[Param('cur', false, D::STRING, 'валюта, обязательна для неавторизованного пользователя, при ее передаче эндпоинт перестает учитывать авторизацию', 'RUB')]
    #[Param('platform', false, D::STRING, 'платформа (социальная сеть)', 'Youtube')]
    #[Param('labels', false, D::STRING, 'список меток', '"LAB1 LAB2 LAB3"')]
    #[Param('counts', true, D::OTHER, 'массив чисел для расчета цен', 'count: [100, 250, 500]')]
    public function tiny(Request $request): ApiResponse
    {
        $request->validate([
            'cur'      => 'string',
            'platform' => 'string',
            'labels'   => 'string',
            'counts'   => 'required|array|min:1',
        ]);

        $cur = $request->input('cur');
        $user = null;
        if (! $cur) {
            $user = maybe_user();
            $cur = $user?->cur;
        }
        if (! $cur) {
            return new ApiError('no currency');
        }

        if (! in_array($cur, [
            Transaction::CUR_RUB,
            Transaction::CUR_USD,
            Transaction::CUR_EUR,
            Transaction::CUR_UZS
        ])) {
            return new ApiError('bad currency');
        }
        $uss = UserService::tagsCached($request->platform, $request->labels);
        $counts = collect($request->counts);
        $costs = $uss->mapWithKeys(function($us) use ($cur, $user, $counts) {
            return [
                $us->tag => ! $us
                    ? 'no such tag'
                    : $counts->mapWithKeys(function($count) use ($cur, $user, $us) {
                        if (!is_numeric($count) || $count < 0) {
                            return [$count => 'bad count'];
                        }
                        $cost = $us->getFinalCostAndDiscount($count, $cur, $user);
                        return [$count => ['cost' => $cost[0], 'discount' => $cost[1]]];
                    })
            ];
        });
        return new ApiSuccess('', $costs);
    }

    #[Group('money')]
    #[Endpoint('prices/orderCost')]
    #[Verbs(D::GET)]
    #[Text('Получить цену будущего заказа')]
    #[Param('cur', false, D::STRING, 'валюта, обязательна для неавторизованного пользователя, при ее передаче эндпоинт перестает учитывать авторизацию', 'RUB')]
    #[Param('tag', true, D::STRING, 'service tag', 'INSTAGRAM_LIKES_MAIN')]
    #[Param('count', true, D::INT, 'count', 100)]
    public function orderCost(Request $request): ApiResponse
    {
        $request->validate([
            'cur'   => 'string',
            'tag'   => 'required|string',
            'count' => 'required|int',
        ]);

        $cur = $request->input('cur');
        $user = null;
        if (! $cur) {
            $user = maybe_user();
            $cur = $user?->cur;
        }
        if (! $cur) {
            return new ApiError('no currency');
        }

        if (! in_array($cur, [
            Transaction::CUR_RUB,
            Transaction::CUR_USD,
            Transaction::CUR_EUR,
            Transaction::CUR_UZS,
        ])) {
            return new ApiError('bad currency');
        }
        if ($request->input('count') < 0) {
            return new ApiError('bad count');
        }
        $us = UserService::findByTagCached($request->input('tag'));
        if (! $us) {
            return new ApiError('no such tag');
        }
        return new ApiSuccess(
            message: '',
            data: $us->getFinalCostAndDiscount($request->input('count'), $cur, $user)
        );
    }
}
