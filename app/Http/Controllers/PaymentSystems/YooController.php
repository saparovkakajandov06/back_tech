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
use App\PaymentSystems\YooPaymentSystem;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YooController extends Controller {
    #[
        Group('payment'),
        Endpoint('deposit/yk'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Пополнить счет из ЛК через YooKassa'),
        Param('amount', true, D::INT),
        Param('success_url', true, D::URL),
        Param('cancel_url', true, D::URL),
    ]
    public function deposit(Request $request, YooPaymentSystem $yooPS): ApiResponse {
        $val = $request->validate([
            'amount'      => 'required|numeric',
            'cancel_url'  => 'required|string',
            'description' => 'required|string',
            'success_url' => 'required|string',
        ]);
        $val['cur'] = Auth::user()->cur;
        return $yooPS->startAuthSession($val);
    }

    #[
        Group('payment'),
        Endpoint('yk_status'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Метод для обработки хуков от yooKassa'),
    ]
    public function hook(Request $request, YooPaymentSystem $yooPS) {
        return $yooPS->handleHook($request);
    }
}
