<?php

namespace App\Http\Controllers\PaymentSystems;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentSystems\PayzePaymentSystem;
use Illuminate\Http\Request;

class PayzeController extends Controller
{
    #[Group('payment')]
    #[Endpoint('payze_status')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для обработки хуков от Payze')]
    public function hook(Request $request, PayzePaymentSystem $payzePS)
    {
        return $payzePS->handleHook($request);
    }
}
