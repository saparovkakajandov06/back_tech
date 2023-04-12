<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Responses\ApiError;
use App\Responses\ApiSuccess;
use App\Services\EventsSchemesService;
use App\User;
use Illuminate\Http\Request;

class SendpulseController extends Controller
{
    private EventsSchemesService $ess;
    private ?User $user;

    public function __construct()
    {
        $this->ess = resolve(EventsSchemesService::class);
        $this->user = maybe_user();
    }

    #[Endpoint('sp/cart')]
    #[Verbs(D::GET)]
    #[Group('other')]
    #[Text('Создание ивента sendpulse брошенная корзина')]
    #[Param('*.tag', true, D::STRING, 'обязателен если корзина НЕ ПУСТАЯ')]
    #[Param('*.cost', true, D::FLOAT, 'обязателен если корзина НЕ ПУСТАЯ')]
    public function createAbandonedСart(Request $request)
    {
        //validate if cart is not clear
        if($request->all()){
            $request->validate([
                '*.tag' => 'required|string',
                '*.cost' => 'required|numeric',
            ]);

            $data = $request->only(['*.tag', '*.cost']);
        }

        if($this->user?->canSendMail()){
            $this->ess->createAbandonedСart($this->user->id, $data ?? []);
        }

        return new ApiSuccess(message: 'ok');
    }

    #[Endpoint('sp/balance')]
    #[Verbs(D::GET)]
    #[Group('other')]
    #[Text('Создание ивента sendpulse не пополнил баланс')]
    #[Param('value', true, D::FLOAT, 'сумма пополнения', '505.31')]
    public function createNotTopUpBalance(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric',
        ]);

        if($this->user?->canSendMail()){
            $this->ess->createNotTopUpBalance($this->user->id, $request->value);
        }

        return new ApiSuccess(message: 'ok');
    }

    #[Endpoint('sp/order')]
    #[Verbs(D::GET)]
    #[Group('other')]
    #[Text('Создание ивента sendpulse не оплатил заказ')]
    #[Param('*.tag', true, D::STRING, 'массив данных со списком тегов, ключ массива любой, тег - строка')]
    #[Param('*.cost', true, D::FLOAT, 'массив данных со списком стоимостей, ключ массива любой, стоимость - число с плавающей точкой')]
    public function createUnpaidOrder(Request $request)
    {
        if(!$request->all()){
            return new ApiError(message: 'no required keys');
        }

        $request->validate([
            '*.tag' => 'required|string',
            '*.cost' => 'required|numeric',
        ]);

        if($this->user?->canSendMail()){
            $data = $request->only(['*.tag', '*.cost']);
            $this->ess->createUnpaidOrder($this->user->id, $data);
        }

        return new ApiSuccess(message: 'ok');
    }
}
