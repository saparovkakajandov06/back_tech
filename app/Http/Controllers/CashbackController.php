<?php

namespace App\Http\Controllers;

use App\Cashback;
use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\MoneyService;
use App\Services\Search\CashbackSearchService;
use App\Transaction;
use App\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @group Cashback
 */
class CashbackController extends Controller
{

    /**
     * User's cashback
     */
    #[Endpoint('user/cashback')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('Transaction')]
    #[Text('Информация о транзакциях кешбека пользователя')]
    #[Param('date_from', false, D::DATE)]
    #[Param('date_to', false, D::DATE)]
    #[Param('types', false, D::STRING)]
    #[Param('offset', false, D::INT)]
    #[Param('limit', false, D::INT)]
    public function transactions(
        Request $request, CashbackSearchService $svc): ApiResponse
    {
        $svc->setUserId(Auth::id())
            ->setDateFrom($request->date_from)
            ->setDateTo($request->date_to)
            ->setTypes($request->types)
            ->setCur(Auth::user()->cur)
            ->setOffset($request->offset)
            ->setLimit($request->limit);

        $transactions = $svc->getResult();
        $last = $transactions->last();

        return new ApiSuccess('Список транзакций кешбека пользователя', [
            'items' => $transactions,
            'meta' => [
                'offset' => $svc->getOffset(),
                'limit' => $svc->getLimit(),
                'total' => $svc->getCount(),
                'initial_date_from_ts' => $last
                    ? $last->created_at->timestamp
                    : 0
            ]
        ]);
    }



    /**
     * User's totals
     */
    #[Endpoint('user/cashback/totals')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('Transaction')]
    #[Text('Список сумм сгруппированных транзакциях кешбека пользователя')]
    public function totals(Request $request)
    {
        $data = CashbackSearchService
            ::getMoneyTotalsByType(Auth::id(), Auth::user()->cur)->all();

        $res = Cashback::withZeros($data);

        return new ApiSuccess(
            'Список сумм сгруппированных транзакций кешбека пользователя',
            $res);
    }
}
