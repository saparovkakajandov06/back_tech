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
use App\PaymentSystems\CentPaymentSystem;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CentController extends Controller {
    #[
        Group('payment'),
        Endpoint('deposit/cent'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Пополнить счет ЛК через cent.app'),
        Param('amount', true, D::INT),
        Param('success_url', true, D::URL),
        Param('cancel_url', true, D::URL)
    ]
    public function deposit(Request $request, CentPaymentSystem $centPS): ApiResponse
    {
        $val = $request->validate([
            'amount'      => 'required|numeric',
            'cancel_url'  => 'required|string',
            'description' => 'required|string',
            'success_url' => 'required|string',
        ]);
        $val['cur'] = Auth::user()->cur;
        return $centPS->startAuthSession($val);
    }

    #[Group('payment')]
    #[Endpoint('cent_status')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Метод для обработки хуков от cent.app')]
    public function hook(Request $request, CentPaymentSystem $centPS)
    {
        return $centPS->handleHook($request);
    }
}
