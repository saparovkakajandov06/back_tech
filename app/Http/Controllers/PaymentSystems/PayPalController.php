<?php

namespace App\Http\Controllers\PaymentSystems;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentSystems\PayPalPaymentSystem;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    #[Group('payment')]
    #[Endpoint('paypal_status')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для обработки хуков от PayPal')]
    public function hook(Request $request, PayPalPaymentSystem $payPalPS)
    {
        return $payPalPS->handleHook($request);
    }
}
