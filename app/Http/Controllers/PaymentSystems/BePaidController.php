<?php

namespace App\Http\Controllers\PaymentSystems;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentSystems\BePaidPaymentSystem;
use Illuminate\Http\Request;

class BePaidController extends Controller
{
    #[Group('payment')]
    #[Endpoint('bepaid_status')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для обработки хуков от BePaid')]
    public function hook(Request $request, BePaidPaymentSystem $paymentSystem)
    {
        return $paymentSystem->handleHook($request);
    }
}
