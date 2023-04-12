<?php

namespace App\Http\Controllers\PaymentSystems;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentSystems\MonerchyPaymentSystem;
use Illuminate\Http\Request;

class MonerchyController extends Controller
{
    #[Group('payment')]
    #[Endpoint('monerchy_status')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для обработки хуков от Monerchy')]
    public function hook(Request $request, MonerchyPaymentSystem $monerchyPaymentSystem)
    {
        return $monerchyPaymentSystem->handleHook($request);
    }
}
