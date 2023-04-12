<?php

namespace App\Http\Controllers;

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
use App\Services\Search\TransactionsSearchService;
use App\Transaction;
use App\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @group Transactions
 */
class TransactionsController extends Controller
{
    private $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Withdraw money
     */
    #[Endpoint('withdrawal')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('Transaction')]
    #[Text('Вывод средств')]
    #[Param('amount', true, D::INT)]
    #[Param('withdraw_method', true, D::INT)]
    #[Param('wallet_number', true, D::INT)]
    #[Param('cur', true, D::STRING)]
    public function withdraw(Request $request, MoneyService $m): ApiResponse
    {
        $user = Auth::user();
        $withdrawType = '';

        $val = $request->validate([
            'amount' => 'required|integer',
            'withdraw_method' => 'required|integer',
            'wallet_number' => 'required|integer',
            'cur' => 'required|string',
        ]);

        $balance = $m->getUserBalance($user, $val['cur']);

        if ($balance < $val['amount']) {
            return new ApiError(Transaction::NOT_ENOUGH_FUNDS);
        } else {
            $amount = -$val['amount'];
            $wallet_num = $val['wallet_number'];

            switch ($val['withdraw_method']) {
                case 1:
                    $withdrawType = Withdraw::BANK_CARD;
                    break;
                case 2:
                    $withdrawType = Withdraw::YANDEX_MONEY;
                    break;
                case 3:
                    $withdrawType = Withdraw::QIWI_WALLET;
                    break;
                case 4:
                    $withdrawType = Withdraw::PHONE_NUMBER;
                    break;
            }

            $m->makeWithdraw($user, $withdrawType, $wallet_num, $amount);

            return new ApiSuccess('ok', [
                'balance' => $m->getUserBalance($user, $val['cur'])
            ]);
        }
    }

    /**
     * User's transactions
     */
    #[Endpoint('user/transactions')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('Transaction')]
    #[Text('Информация о транзакциях пользователя')]
    #[Param('date_from', false, D::DATE)]
    #[Param('date_to', false, D::DATE)]
    #[Param('types', false, D::STRING)]
    #[Param('offset', false, D::INT)]
    #[Param('limit', false, D::INT)]
    public function transactions(
        Request $request, TransactionsSearchService $svc): ApiResponse
    {
        $svc->setUserId(Auth::id())
            ->setDateFrom($request->date_from)
            ->setDateTo($request->date_to)
            ->setTypes($request->types)
            ->setOffset($request->offset)
            ->setLimit($request->limit);

        $transactions = $svc->getResult();
        $last = $transactions->last();

        return new ApiSuccess('Список транзакций пользователя', [
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
     * @deprecated
     */
    public function transactionsOld(Request $request): ApiResponse
    {
        [$total, $transactions] =
            $this->transactionRepository->getGroupByUser(
                Auth::id(),
                $request->get('date_from'),
                $request->get('date_to'),
                $request->get('transaction_group'),
                Str::upper($request->get('cur')),
                $request->get('offset', 0),
                $request->get('limit', 10)
            );
        $last = $transactions->last();
        $data = [
            'items' => $transactions,
            'meta' => [
                'offset' => $request->get('offset', 0),
                'limit' => $request->get('limit', 10),
                'total' => $total,
                'initial_date_from_ts' => $last
                    ? $last->created_at->timestamp
                    : 0
            ]
        ];

        return new ApiSuccess('Список транзакций пользователя', $data);
    }

    /**
     * User's totals
     */
    #[Endpoint('user/transactions/totals')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Group('Transaction')]
    #[Text('Список сумм сгруппированных транзакций пользователя')]
    public function totals(Request $request)
    {
        $data = TransactionsSearchService
            ::getMoneyTotalsByType(Auth::id(), Auth::user()->cur)->all();

        $res = Transaction::withZeros($data);

        return new ApiSuccess(
            'Список сумм сгруппированных транзакций пользователя',
            $res);
    }
}
