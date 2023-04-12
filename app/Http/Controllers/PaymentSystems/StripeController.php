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
use App\PaymentSystems\StripePaymentSystem;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller {
    #[
        Group('payment'),
        Endpoint('deposit/stripe'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Пополнить счет ЛК через Stripe'),
        Param('amount', true, D::INT),
        Param('success_url', true, D::URL),
        Param('cancel_url', true, D::URL)
    ]
    public function deposit(Request $request, StripePaymentSystem $stripePS): ApiResponse
    {
        $val = $request->validate([
            'amount'      => 'required|numeric',
            'cancel_url'  => 'required|string',
            'description' => 'required|string',
            'success_url' => 'required|string',
        ]);
        $val['cur'] = Auth::user()->cur;
        return $stripePS->startAuthSession($val);
    }

    #[Group('payment')]
    #[Endpoint('stripe_status')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для обработки хуков от Stripe')]
    public function hook(Request $request, StripePaymentSystem $stripePS)
    {
        return $stripePS->handleHook($request);
    }
}
