<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Controllers\PaymentSystems;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentSystems\FondyPaymentSystem;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FondyController extends Controller {
    #[
        Group('payment'),
        Endpoint('deposit/fondy'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Пополнить счет ЛК через Fondy'),
        Param('amount', true, D::INT),
        Param('success_url', true, D::URL),
        Param('cancel_url', true, D::URL)
    ]
    public function deposit(Request $request, FondyPaymentSystem $fondyPS): ApiResponse
    {
        $val = $request->validate([
            'amount'      => 'required|numeric',
            'cancel_url'  => 'required|string',
            'description' => 'required|string',
            'success_url' => 'required|string',
        ]);
        $val['cur'] = Auth::user()->cur;
        return $fondyPS->startAuthSession($val);
    }

    #[Group('payment')]
    #[Endpoint('fondy_status')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для обработки хуков от Fondy')]
    public function hook(Request $request, FondyPaymentSystem $fondyPS)
    {
        return $fondyPS->handleHook($request);
    }

    #[Group('payment')]
    #[Endpoint('fondy_redirect')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для перенаправления пользователля после оплаты через Fondy')]
    public function redirect(Request $request)
    {
        return response('', 303, ['Location' => urldecode($request->input('to'))]);
    }
}
