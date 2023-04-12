<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Domain\Splitters\ISplitter;
use App\Exceptions\Reportable\ConfigurationException;
use App\Http\Middleware\SetRegionMW;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\DistributionService;
use App\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @group user_services
 */
class UserServicesController extends Controller
{
    #[Endpoint('user_services/find')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('user services')]
    #[Text('Получить список пользовательских сервисов')]
    #[Param('reg', true, D::STRING,
        "регион пользователя ('CIS', 'EUROPE', 'USA', 'TURKEY', 'BRAZIL', 'UKRAINE', 'UNKNOWN')", 'CIS')]
    #[Param('labels', false, D::STRING, 'список меток', '"LAB1 LAB2 LAB3"')]
    #[Param('tag', false, D::STRING, 'тэг сервиса', 'INSTAGRAM_LIKES_LK')]
    #[Param('platform', false, D::STRING, 'платформа (социальная сеть)', 'Youtube')]
    public function find(Request $request)
    {
        $q = UserService::query();

        $region = $request->reg;
        if (!in_array($region, SetRegionMW::REGIONS)) {
            return new ApiError('Bad region', ['region' => $region]);
        }

        if ($request->labels) {
            Str::of($request->labels)
                ->upper()
                ->explode(' ')
                ->each(fn($label) => $q->withLabel($label));
        }

        if ($request->tag) {
            $q->where('tag', $request->tag);
        }

        if ($platform = $request->platform) {
            $q->where('platform', $platform);
        }

        $cur = SetRegionMW::getCurrency($region);

        $services = $q->with('price')->orderBy('id')->get();

        $services->each(function ($s) use ($cur) {
            $s->price_list = $s->price->$cur ?? [];
            $s->cur = $cur;
            unset($s->price);
        });

        return new ApiSuccess('Пользовательские сервисы', $services);
    }

    #[Group('user services')]
    #[Endpoint('user_services/tiny')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Text('Получить минимальные данные о пользовательских сервисах')]
    #[Param('platform', false, D::STRING, 'платформа (социальная сеть)', 'Youtube')]
    #[Param('labels', false, D::STRING, 'список меток', '"LAB1 LAB2 LAB3"')]
    public function tiny(Request $request)
    {
        return new ApiSuccess(
            message: '',
            data: UserService::tinyCached($request->platform, $request->labels)
        );
    }

    #[Group('user services')]
    #[Endpoint('user_services/tags')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Text('Получить теги пользовательских сервисов')]
    #[Param('platform', false, D::STRING, 'платформа (социальная сеть)', 'Youtube')]
    #[Param('labels', false, D::STRING, 'список меток', '"LAB1 LAB2 LAB3"')]
    public function tags(Request $request)
    {
        return new ApiSuccess(
            message: '',
            data: UserService::tagsCached($request->platform, $request->labels)->map(fn($service) => $service->tag)
        );
    }

    /**
     * @OA\Post(
     *      path="/api/user_services/{tag}",
     *      operationId="update",
     *      summary="Update UserService",
     *      tags={"UserServices"},
     *      description="Админ: обновить данные пользовательского сервиса",
     *      @OA\Parameter(
     *          name="tag",
     *          in="query",
     *          description="The tag parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="string"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Updated"
     *      ),
     *     )
     * Update UserService
     */
    #[Endpoint('user_services/{tag}')]
    #[Verbs(D::POST)]
    #[Group('user services')]
    #[Role('ROLE_MODERATOR')]
    #[Text('Метод для Администратора')]
    #[Text('Обновить данные пользовательского сервиса')]
    #[Param('tag', true, D::STRING, '', 'INSTAGRAM_LIKES_MAIN')]
    public function update(Request $request, $tag, DistributionService $service): ApiResponse
    {
        $us = UserService::where('tag', $tag)
            ->firstOrFail();

        $data = $request->all();

        /** @var ISplitter $class */
        $class = $data['splitter'];
        $services = $service->data($data['config'])->all();

        try {
            $class::throw(count($services));
        } catch (ConfigurationException $e) {
            Log::error("{$e->getMessage()} file {$e->getFile()} line {$e->getLine()}");
            return new ApiError('Service not updated. Check service count');
        }

        $us->update($request->all());

        return new ApiSuccess('Updated', ['user_service' => $us]);
    }

    /**
     * @OA\Post(
     *      path="/api/user_services/disconnect",
     *      operationId="disconnectById",
     *      summary="User services disconnectById",
     *      tags={"UserServices"},
     *      description="Отключение пользовательского сервиса по id",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="The visible parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success"
     *      ),
     *     )
     * User services disconnectById
     */
    #[Endpoint('user_services/disconnect')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MODERATOR')]
    #[Group('user services')]
    #[Text('Отключение пользовательского сервиса по id')]
    #[Param('id', true, D::INT)]
    public function disconnectById(Request $request): ApiResponse
    {
        $service = UserService::where('id', $request->id)
            ->update(['visible' => 0]);

        if (!$service) {
            return new ApiError('Service not disconnected');
        }

        return new ApiSuccess('success');
    }

    /**
     * @OA\Post(
     *      path="/api/user_services/connect",
     *      operationId="connectById",
     *      summary="User services connectById",
     *      tags={"UserServices"},
     *      description="Включение пользовательского сервиса по id",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="The visible parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="number"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success"
     *      ),
     *     )
     * User services connectById
     */
    #[Endpoint('user_services/connect')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MODERATOR')]
    #[Group('user services')]
    #[Text('Включение пользовательского сервиса по id')]
    #[Param('id', true, D::INT)]
    public function connectById(Request $request): ApiResponse
    {
        $service = UserService::where('id', $request->id)
            ->update(['visible' => 1]);

        if (!$service) {
            return new ApiError('Service not disconnected');
        }

        return new ApiSuccess('success');
    }

    /**
     * @OA\Post(
     *      path="/api/moderator/updatePrecent",
     *      operationId="updatePercentAndCountGroup",
     *      summary="Bulk change of order percentages",
     *      tags={"UserServices"},
     *      description="Bulk change of order percentages, min, max or server shutdown",
     *      @OA\Parameter(
     *          name="request",
     *          in="query",
     *          description="The request parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="object"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success"
     *      ),
     *     )
     * Bulk change of order percentages
     */
    #[Endpoint('moderator/updatePrecent')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MODERATOR')]
    #[Group('user services')]
    #[Text('Массовое изменение процента заказов, мин., макс, или отключение сервера ')]
    #[Param('id', true, D::INT)]
    public function updatePercentAndCountGroup(Request $request): ApiResponse
    {
        $temp = [];
        $likesGroup = ['INSTAGRAM_LIKES_MAIN', 'INSTAGRAM_MULTI_LIKES_MAIN'];
        $likesGroup_lk = ['INSTAGRAM_LIKES_LK', 'INSTAGRAM_MULTI_LIKES_LK'];
        $followGroup = ['INSTAGRAM_SUBS_LK', 'INSTAGRAM_SUBS_MAIN'];
        $tktLikeGroup = ['TIKTOK_LIKES_LK', 'TIKTOK_LIKES_MAIN'];
        $tktFollowGroup = ['TIKTOK_SUBS_LK', 'TIKTOK_SUBS_MAIN'];

        $data = $request->all();

        switch ($data['type']) {
            case 'likes':
                $typeGroup = $likesGroup;
                break;
            case 'likes-cp':
                $typeGroup = $likesGroup_lk;
                break;
            case 'like_tkt':
                $typeGroup = $tktLikeGroup;
                break;
            case 'follow_tkt':
                $typeGroup = $tktFollowGroup;
                break;
            default:
                $typeGroup = $followGroup;
        }
        //$typeGroup = $data['type'] === 'likes' ? $likesGroup : $followGroup;

        for ($i = 0; $i < count($typeGroup); $i++) {
            $res[] = json_decode(UserService::where('tag', $typeGroup[$i])->firstOrFail());

            $countServers = 0;

            if ($data['method'] === "percent") {
                foreach ($res[$i]->config as $key => $value) {
                    $value->count_extra_percent = $data['precent'][$countServers];
                    $countServers++;
                }
            } elseif ($data['method'] === "count") {
                foreach ($res[$i]->config as $key => $value) {
                    $value->max = $data['max'][$countServers];
                    $value->isEnabled = $data['isEnabled'][$countServers];
                    $countServers++;
                }
            } else {
                return new ApiError('Method not fount');
            }

            UserService::where('tag', $typeGroup[$i])->update(['config' => json_encode($res[$i]->config)]);

        }

        return new ApiSuccess('success', ['user_service' => $res]);

    }

    /**
     * @OA\Get(
     *      path="/api/moderator/getPrecent",
     *      operationId="getGroupForModerate",
     *      summary="Getting a group",
     *      tags={"UserServices"},
     *      description="Получаем группу, либо Лайки либо подписчики(группа выборочная)",
     *      @OA\Parameter(
     *          name="request",
     *          in="query",
     *          description="The request object parameter in query",
     *          required=true,
     *          @OA\Schema(
     *             type="object"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success"
     *      ),
     *     )
     * Getting a group
     */
    #[Endpoint('moderator/getPrecent')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MODERATOR')]
    #[Group('user services')]
    #[Text('Получаем группу, либо Лайки либо подписчики(группа выборочная)')]
    #[Param('id', true, D::INT)]
    public function getGroupForModerate(Request $request): ApiResponse
    {
        $likesGroup = 'INSTAGRAM_LIKES_MAIN';
        $likesGroup_lk = 'INSTAGRAM_LIKES_LK';
        $followGroup = 'INSTAGRAM_SUBS_LK';
        $tktLikeGroup = 'TIKTOK_LIKES_LK';
        $tktFollowGroup = 'TIKTOK_SUBS_LK';

        $data = $request->all();

        switch ($data['type']) {
            case 'likes':
                $typeGroup = $likesGroup;
                break;
            case 'likes-cp':
                $typeGroup = $likesGroup_lk;
                break;
            case 'like_tkt':
                $typeGroup = $tktLikeGroup;
                break;
            case 'follow_tkt':
                $typeGroup = $tktFollowGroup;
                break;
            default:
                $typeGroup = $followGroup;
        }

        $temp = json_decode(UserService::where('tag', $typeGroup)->firstOrFail());

        foreach ($temp->config as $key => $value) {
            $res[$key] = $value;
            // todo а если буквы нет?
            $res[$key]->name = substr($key, strpos($key, "\A") + 2);
            $res[$key]->type = $data['type'];
        }

        return new ApiSuccess('success', ['user_service' => $res]);
    }

    #[Endpoint('user_services/{tag}/labels/list')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MODERATOR')]
    #[Group('user services')]
    #[Text('Получить по тегу список лейблов для сервиса')]
    #[Param('tag', true, D::STRING)]
    public function listLabels(Request $request): ApiResponse
    {
        $service = UserService::where('tag', $request->tag)->firstOrFail();

        return new ApiSuccess('success', [
            'tag' => $service->labels,
        ]);
    }

    #[Endpoint('user_services/{tag}/labels/add')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MODERATOR')]
    #[Group('user services')]
    #[Text('Добавить лейбл сервису')]
    #[Param('tag', true, D::STRING)]
    #[Param('labels', true, D::TYPE_ARRAY)]
    public function addLabels(Request $request): ApiResponse
    {
        $request->validate([
            'labels' => 'required|array|min:1',
        ]);
        $service = UserService::where('tag', $request->tag)
            ->firstOrFail();
        foreach ($request->labels as $label) {
            if (! $service->hasLabel($label)) {
                $service->addLabel($label);
            }
        }

        return new ApiSuccess('success', [
            'tag' => $service->refresh()->labels,
        ]);
    }

    #[Endpoint('user_services/{tag}/labels/remove')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MODERATOR')]
    #[Group('user services')]
    #[Text('Удалить лейбл у сервиса')]
    #[Param('tag', true, D::STRING)]
    #[Param('labels', true, D::TYPE_ARRAY)]
    public function removeLabels(Request $request): ApiResponse
    {
        $request->validate([
            'labels' => 'required|array|min:1',
        ]);
        $service = UserService::where('tag', $request->tag)
            ->firstOrFail();
        foreach ($request->labels as $label) {
            if ($service->hasLabel($label)) {
                $service->removeLabel($label);
            }
        }

        return new ApiSuccess('success', [
            'tag' => $service->refresh()->labels,
        ]);
    }
}
