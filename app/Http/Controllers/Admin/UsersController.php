<?php

namespace App\Http\Controllers\Admin;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateApiRequest;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\Money\Services\TransactionsService;
use App\Transaction as Tr;
use App\Payment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * @group users
 */
class UsersController extends Controller
{

    // Find user by name or email
    // for tests
    #[Endpoint('admin/users')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MANAGER')]
    #[Group('user')]
    #[Text('Метод для Администратора')]
    #[Text('Поиск юзера по имени или почте (тестовый метод)')]
    #[Param('name', false, D::INT, 'обязательный параметр если не передается почта юзера')]
    #[Param('email', false, D::INT, 'обязательный параметр если не передается имя юзера')]
    public function index(Request $request, TransactionsService $money): ApiResponse
    {
        $request->validate([
            'name' => 'required_without:email',
            'email' => 'required_without:name|email',
        ]);

        if ($request->get('name') !== null) {
            $query = $request->get('name');
            $typeSearch = 'name';
        } else {
            $query = $request->get('email');
            $typeSearch = 'email';
        }

        $users = User::where($typeSearch, 'like', '%' . $query . '%')
            ->select(['id', 'name', 'email', 'premium_status_id', 'lang'])
            ->get();

        $data = $users->map(fn($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'lang' =>  $user->lang,
            'balance_rub' => $money->sum($user, Tr::CUR_RUB),
            'balance_usd' => $money->sum($user, Tr::CUR_USD),
            'sum_balance_rub' => $money->sum($user, Tr::CUR_RUB),
            'sum_balance_usd' => $money->sum($user, Tr::CUR_USD),
            'status' => $user->premium_status_id,
        ]);

        if (count($data)) {
            return new ApiSuccess('Пользователи', $data);
        } else {
            return new ApiError(User::notFound);
        }
    }

    #[Group('user')]
    #[Endpoint('admin/find')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MANAGER')]
    #[Text('Метод для Администратора')]
    #[Text('Поиск юзера по имени или почте')]
    #[Param('name', false, D::INT, 'обязательный параметр если не передается почта юзера')]
    #[Param('email', false, D::INT, 'обязательный параметр если не передается имя юзера, минимум 4 символа')]
    public function find(Request $request): ApiResponse
    {
        $request->validate([
            'name' => 'required_without_all:email,ip',
            'email' => 'required_without_all:name,ip|min:4',
            'ip' => 'required_without_all:name,email',
        ]);

        $q = DB::table('users');

        if ($name = $request->name) {
            if (Str::length($name) <= 3) {
                $q->where('users.name', $name);
            } else {
                $q->where('users.name', 'ilike', "%$name%");
            }
        }

        if ($email = $request->email) {
            $q->where('users.email', 'ilike', "%$email%");
        }

        $qPayments = DB::table('payments')
            ->select('user_id');

        if ($request->has('ip')) {
            $ip = $request->get('ip');
            $qPayments = $qPayments
                ->where('payments.ip', 'ILIKE', "%$ip%")
                ->where(['payments.status' => Payment::STATUS_PAYMENT_SUCCEEDED]);
        }

        $qPayments = $qPayments->groupBy('user_id');

        $qBalances = DB::table('transactions')
            ->select('user_id', DB::raw('SUM(amount) as balance'), 'cur')
            ->groupBy('user_id', 'cur');

        $qInflowPayments = DB::table('transactions')
            ->select('user_id', DB::raw('SUM(amount) as sum_inflow'), 'cur')
            ->whereIn('type', [Tr::INFLOW_PAYMENT, Tr::INFLOW_CREATE])
            ->groupBy('user_id', 'cur');

        $qInflowRefBonuses = DB::table('transactions')
            ->select('user_id', DB::raw('SUM(amount) as sum_bonus'), 'cur')
            ->where('type', Tr::INFLOW_REF_BONUS)
            ->groupBy('user_id', 'cur');

        $q->leftJoinSub($qBalances, 'balances', function ($join) {
            $join->on('users.id', '=', 'balances.user_id')
                ->on('users.cur', '=', 'balances.cur');
        })->leftJoinSub($qInflowPayments, 'inflows', function ($join) {
            $join->on('users.id', '=', 'inflows.user_id')
                ->on('users.cur', '=', 'inflows.cur');
        })->leftJoinSub($qInflowRefBonuses, 'rb', function ($join) {
            $join->on('users.id', '=', 'rb.user_id')
                ->on('users.cur', '=', 'rb.cur');
        });

        if ($request->has('ip')) {
            $q->joinSub($qPayments, 'payments', function ($join) use($ip) {
                $join->on('users.id', '=', 'payments.user_id');
                /*    ->where('payments.ip', 'ILIKE', "%$ip%")
                    ->where(['payments.status' => Payment::STATUS_PAYMENT_SUCCEEDED]);*/
            });
        }

        $q->select(
            'users.id',
            'users.name',
            'users.email',
            'users.lang',
            DB::raw('COALESCE(balances.balance, 0) as balance'),
            DB::raw('COALESCE(inflows.sum_inflow, 0) as sum_inflow'),
            DB::raw('COALESCE(rb.sum_bonus, 0) as sum_bonus'),
            'users.premium_status_id as status',
            'users.cur',
        );

        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);

        return new ApiSuccess('ok', [
            'count' => $q->count(),
            'offset' => $offset,
            'limit' => $limit,
            'users' => $q->offset($offset)->limit($limit)->get(),
        ]);
    }

    /**
     * @param PaginateApiRequest $request
     * @param $user_id
     * @return ApiError|ApiSuccess
     */
    #[Endpoint('admin/user/{user_id}/transactions')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MANAGER')]
    #[Group('money')]
    #[Text('Метод для Администратора')]
    #[Text('Получить список транзакций юзера')]
    #[Param('user_id', true, D::INT)]
    #[Param('limit', false, D::INT)]
    #[Param('offset', false, D::INT)]
    public function transactions(Request $request, $user_id)
    {
        try {
            $user = User::findOrFail((int)$user_id);

            $count = Tr::where('user_id', $user->id)->count();
            $items = Tr::where('user_id', $user->id)
                    ->orderBy('id', 'desc')
                    ->offset($request->offset ?? 0)
                    ->limit($request->limit ?? 10)
                    ->get();

            return new ApiSuccess('transactions', [
                'items' => $items,
                'count' => $count,
            ]);
        }
        catch (Throwable $exception){
            return new ApiError($exception->getMessage(), $exception);
        }
    }

    /**
     * @param PaginateApiRequest $request
     * @param $user_id
     * @return ApiError|ApiSuccess
     */
    #[Endpoint('admin/user/{user_id}/payments')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MANAGER')]
    #[Group('money')]
    #[Text('Метод для Администратора')]
    #[Text('Получить список платежей юзера')]
    #[Param('user_id', true, D::INT)]
    #[Param('limit', false, D::INT)]
    #[Param('offset', false, D::INT)]
    public function payments(Request $request, $user_id): ApiError|ApiSuccess
    {
        try {
            $user = User::findOrFail((int)$user_id);

            $query = Payment::query()
                ->where('user_id', $user->id)
                ->where(['status' => Payment::STATUS_PAYMENT_SUCCEEDED]);

            $count = $query->count();
            $items = $query
                ->orderBy('id', 'desc')
                ->offset($request->offset ?? 0)
                ->limit($request->limit ?? 10)
                ->get();

            return new ApiSuccess('payments', [
                'items' => $items,
                'count' => $count,
            ]);
        } catch (Throwable $exception){
            return new ApiError($exception->getMessage(), $exception);
        }
    }
}
