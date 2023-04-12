<?php

namespace App\Http\Controllers\PaymentSystems;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentSystems\ConnectumPaymentSystem;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ConnectumController extends Controller
{
    #[
        Group('payment'),
        Endpoint('deposit/connectum'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Пополнить счет ЛК через Connectum'),
        Param('amount', true, D::INT),
        Param('success_url', true, D::URL),
        Param('cancel_url', true, D::URL)
    ]
    public function deposit(Request $request, ConnectumPaymentSystem $connectumPS): ApiResponse
    {
        $val = $request->validate([
            'amount' => 'required|numeric',
            'waiting_url' => 'required|string',
            'cancel_url' => 'required|string',
            'description' => 'required|string',
            'success_url' => 'required|string',
        ]);
        $val['cur'] = Auth::user()->cur;
        return $connectumPS->startAuthSession($val);
    }


    #[Group('payment')]
    #[Endpoint('stripe_status')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для обработки хуков от Stripe')]
    public function hook(Request $request, $order, ConnectumPaymentSystem $connectumPS)
    {
        return $connectumPS->handleHookConnectum($request, $order);
    }

    #[Group('payment')]
    #[Endpoint('connectum_redirect')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для перенаправления пользователля после оплаты через Fondy')]
    public function redirect(Request $request, ConnectumPaymentSystem $connectumPS)
    {
        if ($order_id = $request->get('order_id', false)) {

            $order = $connectumPS->getOrder($order_id);

            return self::hook($request, $order, $connectumPS);

        }

        return response('', 303, ['Location' => urldecode($request->input('to'))]);
    }
}
