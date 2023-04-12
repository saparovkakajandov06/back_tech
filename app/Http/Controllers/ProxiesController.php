<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App;
use App\Proxy;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use Illuminate\Http\Request;

class ProxiesController extends Controller
{
    #[Endpoint('proxies')]
    #[Verbs(D::GET)]
    #[Role('ROLE_PROXY')]
    #[Group('proxy')]
    #[Text('Получить список прокси')]
    #[Param('limit', false, D::INT)]
    #[Param('offset', false, D::INT)]
    public function list(Request $request): ApiResponse
    {
        $offset = (int) $request->get('offset', 0);
        $limit  = (int) $request->get('limit', 10);

        $proxies = Proxy::list($offset, $limit);
        
        $result = [
            'items' => $proxies,
            'meta' => [
                'offset'  => $offset,
                'limit'   => $limit,
                'total'   => Proxy::count(),
            ]
        ];
        
        return new ApiSuccess('List of proxies', $result);
    }
    #[Endpoint('proxies')]
    #[Verbs(D::POST)]
    #[Role('ROLE_PROXY')]
    #[Group('proxy')]
    #[Text('Создание прокси')]
    #[Param('url', true, D::URL)]
    public function store(Request $request): ApiResponse
    {
        $request->validate([
            'url'           => 'required'
        ]);

        $proxy = Proxy::create($request->all());

        return new ApiSuccess('created', ['id' => $proxy->id]);
    }
    #[Endpoint('proxies/{id}')]
    #[Verbs(D::POST)]
    #[Role('ROLE_PROXY')]
    #[Group('proxy')]
    #[Text('Обновление прокси')]
    #[Param('id', true, D::INT)]
    #[Param('url', true, D::URL)]
    public function update(Request $request, $id): ApiResponse
    {
        $proxy = Proxy::findOrfail($id);
        $proxy->update($request->all());

        return new ApiSuccess('updated', ['id' => $proxy->id]);
    }
    #[Endpoint('proxies/{id}')]
    #[Verbs(D::POST)]
    #[Role('ROLE_PROXY')]
    #[Group('proxy')]
    #[Text('Удаление прокси')]
    #[Param('id', true, D::INT)]
    public function destroy($id): ApiResponse
    {
        $proxy = Proxy::findOrFail($id);
        $proxy->delete();

        return new ApiSuccess('deleted', ['id' => $proxy->id]);
    }
}
