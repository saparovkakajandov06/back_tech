<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Exceptions\Reportable\EmailExistsException;
use App\Exceptions\Reportable\NameExistsException;
use App\Exceptions\TException;
use App\Responses\ApiResponse;
use App\Role\UserRole;
use App\Scraper\Simple\BestExperienceTiktokScraper;
use App\Scraper\Simple\InstagramBoboScraper;
use App\Services\Money\Services\CashbackService;
use App\Services\Money\Services\TransactionsService;
use App\Services\MoneyService;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Responses\ApiError;
use App\Responses\ApiSuccess;
use App\Transaction as Tr;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Mail\NewPasswordEmail;

/**
 * @group users
 */
class UsersController extends Controller
{
    private $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @OA\Get(
     *      path="/api/users",
     *      operationId="index",
     *      tags={"User"},
     *      summary="Get List of users",
     *      description="Returns List of users",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *     )
     * Returns list of users
     */
    public function index()
    {
        return User::all();
    }

    #[Group('user')]
    #[Endpoint('user')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Text('Информация о текущем пользователе')]
    public function details(TransactionsService $m, CashbackService $c)
    {
        $user = Auth::user();

        if (stristr($user->avatar, 'users')) {
            $user->avatar = url('storage/' . $user->avatar);
        }

        $user->balance = $m->sum($user, $user->cur);
        $user->cashback = $c->sum($user, $user->cur);
        $user->payments_sum = $m->paymentsSum($user, $user->cur);

        return response()->json(['success' => $user], Response::HTTP_OK);
    }

    #[Group('user')]
    #[Endpoint('user')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Смена почты и логина текущему пользователю')]
    #[Param('name', false, D::STRING)]
    #[Param('email', false, D::STRING)]
    public function update(Request $request, InstagramBoboScraper $igScraper, BestExperienceTiktokScraper $tiktokScraper)
    {
        $request->validate([
            'name' => 'string',
            'email' => 'email',
            'ig_logins' => 'array',
            'tiktok_logins' => 'array'
        ]);

        $user = Auth::user();
        if ($request->filled('name')) {
            if (User::where('name', 'ILIKE', $request->name)->exists()) {
                throw new NameExistsException();
            }
            $user->update($request->only('name'));
        }
        if ($request->filled('email')) {
            if (User::where('email', 'ILIKE', $request->email)->exists()) {
                throw new EmailExistsException();
            }
            $user->update($request->only('email'));
        }

        $update = [];
        $params = $user->params;
        $login = null;

        $scraper = $igScraper;

        if ($request->filled('tiktok_logins')) {
            $params['tiktok_logins'] = $request['tiktok_logins'];
            $scraper = $tiktokScraper;

            $login = $request['tiktok_logins'][0];
            $update['params'] = $params;
        }

        if ($request->filled('ig_logins')) {
            $params['ig_logins'] = $request['ig_logins'];

            $login = $request['ig_logins'][0];
            $update['params'] = $params;
        }


        if (!isset($user->avatar) && $login) {
            try {
                $profile = $scraper->profile($login);
                $response = app(ScraperController::class)->getProxyRequest($profile['img_hd']);

                if (!Str::startsWith('image', $response->header('Content-Type'))) {
                    throw new Exception();
                }

                $path = 'users/'. $login . '_' . now()->toISOString() .'.jpg';
                Storage::disk('public')->put($path, $response->getBody());
                $update['avatar'] = $path;
            } catch (Exception $e) {
                //
            }
        }

        if (count($update)) {
            $user->update($update);
        }

        return ['success' => $user];
    }

    #[Group('user')]
    #[Endpoint('user2')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Text('Информация о текущем пользователе')]
    public function details2(MoneyService $m)
    {
        $user = Auth::user();

        if (stristr($user->avatar, 'users')) {
            $user->avatar = url('storage/' . $user->avatar);
        }

        $user->balance_rub = $m->getUserBalance($user, Tr::CUR_RUB);
        $user->balance_usd = $m->getUserBalance($user, Tr::CUR_USD);
        $user->payments_sum_rub = $m->getPaymentsSum($user, Tr::CUR_RUB);
        $user->payments_sum_usd = $m->getPaymentsSum($user, Tr::CUR_USD);

        return new ApiSuccess('Authentificated user', $user);
    }

    #[Group('user')]
    #[Endpoint('user/refs')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Text('Информация о реферальных начислениях пользователя')]
    #[Param('offset', true, D::INT)]
    #[Param('limit', true, D::INT)]
    public function getRefs(Request $request)
    {
        $transactionsOffset = $request->get('offset', 0);
        $transactionsLimit  = $request->get('limit', 10);

        return new ApiSuccess('',
            $this->transactionRepository->getRefBonuses(
                Auth::id(),
                $transactionsOffset,
                $transactionsLimit,
            )
        );
    }

    #[Group('user')]
    #[Endpoint('user/refs/total')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ANY')]
    #[Text('Общее количество рефералов пользователя')]
    public function getRefsCount(Request $request): ApiResponse
    {
        $refs = User::where('parent_id', Auth::id())->count();
        return new ApiSuccess('', [
            'total' => $refs,
            'sum' => $this->transactionRepository->getRefBonusesTotal(Auth::id())
        ]);
    }

    #[Group('money')]
    #[Endpoint('admin/users/balance')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MODERATOR')]
    #[Text('Метод для изменения баланаса юзера')]
    #[Param('id', true, D::INT)]
    #[Param('action', true, D::STRING, 'plus or minus')]
    #[Param('value', true, D::INT)]
    public function updateBalance(Request $request, MoneyService $money)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|integer',
            'action' => ['required', 'string', Rule::in(['plus', 'minus'])],
            'value'  => 'required|regex:/^\d{1,13}(\.\d{1,4})?$/',
        ]);

        if ($validator->fails()) {
            return new ApiError(__('s.invalid_data'), $validator->errors());
        }

        $user = User::findOrFail($request->id);

        switch($request->action) {
            case 'plus':
                $money->inflow(
                    $user,
                    $request->value,
                    $user->cur,
                    Tr::INFLOW_CREATE, "Admin created money");
                break;
            case 'minus':
                $money->outflow(
                    $user,
                    (-1) * $request->value,
                    $user->cur,
                    Tr::OUTFLOW_DESTROY, "Admin destroyed money");
                break;
        }

        return new ApiSuccess('ok', [
            'user' => $user,
            'balance' => $money->getUserBalance($user, $user->cur),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @deprecated
     */
    public function addInAccount(Request $request)
    {
        $request->validate([
            'host' => 'required',
            'in_token' => 'required',
        ]);
        $url = "https://ulogin.ru/token.php?host=$request->host&token=$request->in_token";
        $data = [];
        try {
            $data = file_get_contents($url);
            $data = json_decode($data, true);
        } catch (Exception $e) {
        }
        if (!$data || !Arr::get($data, 'uid')) {
            return response()->json(['error' => 'not login'], Response::HTTP_OK);
        }
        $user = Auth::user();
        $user->in_id = Arr::get($data, 'uid');
        $user->instagram_login = Arr::get($data, 'nickname');
        $user->save();
        return response()->json(['success' => $user], Response::HTTP_OK);
    }

    #[Group('user')]
    #[Endpoint('user/roles/all')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ADMIN')]
    #[Text('Получить список всех имеющихся ролей (Moderator, Manager, Admin)')]
    public function allRoles()
    {
        return array_keys(UserRole::$roleHierarchy);
    }

    #[Group('user')]
    #[Endpoint('user/{id}/roles')]
    #[Verbs(D::GET)]
    #[Role('ROLE_ADMIN')]
    #[Text('Получить список ролей юзера (Moderator, Manager, Admin)')]
    #[Param('id', true, D::INT)]
    public function getRoles(Request $request, string $id): ApiResponse
    {
        $user = User::findOrFail($id);
        return new ApiSuccess('User roles', $user->getRolesFlat());
    }

    #[Group('user')]
    #[Endpoint('user/{id}/roles')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ADMIN')]
    #[Text('Добавить роль юзеру')]
    #[Param('id', true, D::INT)]
    #[Param('role', true, D::STRING)]
    public function addRole(Request $request, string $id): ApiResponse
    {
        $request->validate(['role' => 'string|required']);
        $newRole = $request->role;

        if (! array_key_exists($newRole, UserRole::$roleHierarchy)) {
            return new ApiError('Unknown role');
        }

        $user = User::findOrFail($id);
        if (! empty($user->roles)) {
            if (in_array($newRole, $user->roles)) {
                return new ApiError('Role exists', $user->roles);
            }
        }

        $user->addRole($newRole);
        $user->save();

        return new ApiSuccess('Role added', $user->getRolesFlat());
    }

    #[Group('user')]
    #[Endpoint('user/{id}/roles')]
    #[Verbs(D::DELETE)]
    #[Role('ROLE_ADMIN')]
    #[Text('Удалить роль у юзера')]
    #[Param('id', true, D::INT)]
    #[Param('role', true, D::STRING)]
    public function removeRole(Request $request, string $id): ApiResponse
    {
        $request->validate(['role' => 'string|required']);
        $toDelete = $request->role;

        $user = User::findOrFail($id);
        $old = $user->roles;

        if (! empty($user->roles)) {
            if (! in_array($toDelete, $user->roles)) {
                return new ApiError('Role does not exist', $user->roles);
            }
        }

        $newRoles = [];
        foreach($user->roles as $role) {
            if ($role !== $toDelete) {
                $newRoles[] = $role;
            }
        }
        $user->update([
            // array without keys
            'roles' => $newRoles,
        ]);

        return new ApiSuccess('Role removed', [
            'role' => $toDelete,
            'old' => $old,
            'new' => $newRoles,
        ]);
    }

    #[Group('user')]
    #[Endpoint('admin/users/password')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MODERATOR')]
    #[Text('Метод для Администратора')]
    #[Text('Обновить пароль юзера')]
    #[Param('id', true, D::INT)]
    #[Param('length', true, D::INT, 'min 8 max 30')]
    #[Param('send', true, D::BOOLEAN, '1 если нужно отправить новый пароль на почту')]
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => 'required|integer',
            'length' => 'required|integer|between:8,30',
            'send'   => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return new ApiError(__('s.invalid_data'), $validator->errors());
        }

        $user = User::findOrFail($request->id);

        $newPassword = Str::random($request->length);

        $user->setPassword($newPassword);

        if($request->send) {
            Mail::to($user->email)
                ->send(new NewPasswordEmail($newPassword, $user->lang));
            $message = 'Новый пароль отправлен на электронную почту '
                . $user->email;
        }
        else {
            $message = 'Новый пароль ' . $newPassword;
        }
        return new ApiSuccess('', $message);
    }
}
