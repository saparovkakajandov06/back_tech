<?php

namespace App\Http\Controllers\PaymentSystems;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentSystems\PoliPaymentSystem;
use Illuminate\Http\Request;

class PayOpController extends Controller {
    #[
        Group('payment'),
        Endpoint('payop_status'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Метод для обработки хуков от PayOp'),
    ]
    public function hook(Request $request, PoliPaymentSystem $poliPS) {
        return $poliPS->handleHook($request);
    }
}
