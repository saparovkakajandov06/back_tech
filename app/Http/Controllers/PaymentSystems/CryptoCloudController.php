<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Controllers\PaymentSystems;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentSystems\CryptoCloudPaymentSystem;

use Illuminate\Http\Request;

class CryptoCloudController extends Controller {

    #[
        Group('payment'),
        Endpoint('cryptocloud_status'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Метод для обработки хуков от CryptoCloud'),
    ]
    public function hook(Request $request, CryptoCloudPaymentSystem $cryptoCloudPaymentSystem) {
        return $cryptoCloudPaymentSystem->handleHook($request);
    }
}
