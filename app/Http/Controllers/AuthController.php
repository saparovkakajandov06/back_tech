<?php

namespace App\Http\Controllers;

use App;
use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Exceptions\NonReportable\InvalidCodeException;
use App\Exceptions\NonReportable\InvalidPasswordException;
use App\Exceptions\NonReportable\MissingParameterException;
use App\Exceptions\Reportable\EmailExistsException;
use App\Exceptions\Reportable\NameExistsException;
use App\Http\Middleware\SetRegionMW;
use App\Mail\GuideEmail;
use App\Mail\ResetEmail;
use App\Mail\WelcomeEmail;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Role\UserRole;
use App\Rules\EmailChars;
use App\Services\SMMAuthService;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Swift_TransportException;
use Illuminate\Http\RedirectResponse;


class AuthController extends Controller
{
    #[Group('auth')]
    #[Endpoint('login/local')]
    #[Verbs(D::POST)]
    #[Text('Локальная авторизация в апи с использование логина/почты и пароля')]
    #[Param('login', true, D::STRING, 'не обязательно если заполнено поле email')]
    #[Param('email', true, D::EMAIL, 'не обязательно если заполнено поле login')]
    #[Param('password', true, D::STRING, 'пароль')]
    public function localLogin(Request $request, SMMAuthService $smmAuth): ApiResponse
    {
        Log::channel('logins')
            ->info('Request '. json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'login' => 'string|required_without:email',
            'email' => 'email|required_without:login',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return new ApiError(__('s.invalid_data'), $validator->errors());
        }

        if ($request->has('login')) {
            $param = Str::lower($request->input('login'));
        } elseif ($request->has('email')) {
            $param = Str::lower($request->input('email'));
        }

        $password = $request->password;

        Log::channel('logins')
            ->info("Local login $param $password");

        if ($token = $smmAuth->localLogin($param, $password)) {
            Log::channel('logins')->info('login ok');

            return new ApiSuccess(__('s.login_ok'), [
                'token' => $token
            ]);
        } else {
            Log::channel('logins')->info('login error');

            return new ApiError(__('s.email_auth_error'));
        }
    }

    #[Group('auth')]
    #[Endpoint('login/facebook')]
    #[Verbs(D::POST)]
    #[Text('Авторизация с использованием facebook sdk.')]
    #[Param('data', true, D::STRING, 'data for authorization')]
    public function facebookLogin(Request $request, SMMAuthService $svc): ApiResponse
    {
        $request->validate([
            'data' => 'required',
        ]);

        $data = $request->input('data');

        if (!$svc->confirmFacebookToken($data['accessToken'], $data['id'])) {
            return new ApiError(__('s.login_error'));
        }

        if ($token = $svc->facebookLogin($data)) {
            return new ApiSuccess('ok', [ 'token' => $token ]);
        }

        return new ApiError(__('s.login_error'));
    }

    #[Group('auth')]
    #[Endpoint('login/vk/callback')]
    #[Verbs(D::GET)]
    #[Text('Получение access_token от VK, используя code от авторизауии.')]
    #[Param('code', true, D::STRING, 'code от авторизации')]
    public function vkLoginCallback(Request $request, SMMAuthService $svc): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required',
                'token' => 'required',
            ]);

            if ($validator->fails()) {
                throw new \Exception('Vk authorization error');
            }

            $code = $request['code'];
            $user_token = $request['token'];
            $vk_user = $svc->getVKToken($code, $user_token);

            $svc->getVKUserAndSave($vk_user, $user_token);
        } catch(\Throwable $e) {
            report($e);
        } finally {
            return redirect('/close');
        }

    }

    #[Group('auth')]
    #[Endpoint('login/vk')]
    #[Verbs(D::POST)]
    #[Text('Авторизация vk.')]
    #[Param('data', true, D::STRING, 'data for authorization')]
    public function vkLogin(Request $request, SMMAuthService $svc): Response | ApiResponse
    {
        $request->validate([
            'data' => 'required',
        ]);

        $data = $request->input('data');
        $result = $svc->vkLogin($data);

        if ('ok' === $result['status']) {
            return new ApiSuccess('ok', [ 'token' => $result['token'] ]);
        }
        if ('error' === $result['status'] && array_key_exists('data', $result)) {
            return response(new ApiError(__('s.login_error'), ['type' => $result['data']]), 401);
        }

        return response(new ApiError(__('s.login_error')), 401);
    }

    #[Group('auth')]
    #[Endpoint('login/facebook/confirm')]
    #[Verbs(D::POST)]
    #[Text('Авторизация с использованием facebook sdk когда в данных отсутствует email.')]
    #[Param('data', true, D::STRING, 'data for authorization')]
    public function facebookLoginWithConfirm(Request $request, SMMAuthService $svc): ApiResponse
    {
        $request->validate([
            'data' => 'required',
        ]);

        $data = $request->input('data');

        if (!$svc->confirmFacebookToken($data['accessToken'], $data['id'])) {
            return new ApiError(__('s.login_error'));
        }

        if ($token = $svc->facebookLoginWithConfirm($data)) {
            return new ApiSuccess('ok', [ 'token' => $token ]);
        }

        return new ApiError(__('s.login_error'));
    }

    #[Group('auth')]
    #[Endpoint('login/facebook/select')]
    #[Verbs(D::POST)]
    #[Text('Выбор метода авторизации, когда отсутсвует email в данных от facebook.')]
    #[Param('data', true, D::STRING, 'data for authorization')]
    public function facebookSelectLogin (Request $request, SMMAuthService $svc): ApiResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'email' => 'required|email',
            'accessToken' => 'required'
        ]);

        if ($validator->fails()) {
            return new ApiError(__('s.invalid_data', $validator->errors()->all()));
        }

        if (!$svc->confirmFacebookToken($request->accessToken, $request->id)) {
            return new ApiError(__('s.login_error'));
        }

        if (User::where('email', 'ILIKE', $request->email)->exists()) {
            return new ApiSuccess('select', [ 'method' => 'confirm' ]);
        }

        return new ApiSuccess('select', [ 'method' => 'create' ]);
    }

    #[Group('auth')]
    #[Endpoint('login/vk/confirm')]
    #[Verbs(D::POST)]
    #[Text('Авторизация с использованием vk когда в данных отсутствует email.')]
    #[Param('data', true, D::STRING, 'data for authorization')]
    public function vkLoginWithConfirm(Request $request, SMMAuthService $svc): ApiResponse
    {
        $request->validate([
            'data' => 'required',
        ]);

        $data = $request->input('data');

        if ($token = $svc->vkLoginWithConfirm($data)) {
            return new ApiSuccess('ok', [ 'token' => $token ]);
        }

        return new ApiError(__('s.login_error'));
    }

    #[Group('auth')]
    #[Endpoint('login/vk/select')]
    #[Verbs(D::POST)]
    #[Text('Выбор метода авторизации, когда отсутсвует email в данных от vk.')]
    #[Param('data', true, D::STRING, 'data for authorization')]
    public function vkSelectLogin (Request $request): ApiResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return new ApiError(__('s.invalid_data', $validator->errors()->all()));
        }

        if (User::where('email', 'ILIKE', $request->email)->exists()) {
            return new ApiSuccess('select', [ 'method' => 'confirm' ]);
        }

        return new ApiSuccess('select', [ 'method' => 'create' ]);
    }

    #[Group('auth')]
    #[Endpoint('register')]
    #[Verbs(D::POST)]
    #[Text('Регистрация нового юзера.')]
    #[Param('login', true, D::STRING, 'лоигн юзера == имя юзера')]
    #[Param('email', true, D::STRING, 'почта юзера')]
    #[Param('password', true, D::STRING, 'пароль')]
    #[Param('password_confirm', true, D::STRING, 'подтверждение пароля = пароль')]
    #[Param('lang', true, D::STRING, 'язык юзера')]
    #[Param('cur', true, D::STRING, 'валюта юзера (RUB/USD/EUR)')]
    #[Param('ref_code', false, D::STRING, 'реферальный код')]
    public function register(Request $request): ApiResponse
    {
        $validator = Validator::make($request->all(), [
            'login'            => 'string',
            'email'            => ['required', new EmailChars()],
            'password'         => 'required',
            'password_confirm' => 'required|same:password',
            'ref_code'         => 'string',
            'lang'             => ['required', 'string', Rule::in(User::LANG)],
            'cur'              => ['required', 'string', Rule::in(Transaction::CUR)],

            'send_bonus'       => 'boolean'
        ]);

        if ($validator->fails()) {
            return new ApiError(__('s.invalid_data'), $validator->errors());
        }

        if (User::where('email', 'ILIKE', $request->email)->exists()) {
            throw new EmailExistsException();
        }

        if ($request->login && User::where('name', 'ILIKE', $request->login)->exists()) {
            throw new NameExistsException();
        }

        $confirmation_code = Str::random(30);

        $r = Str::random(strlen($request->password));
        $currency = Str::upper($request->cur);

        if ($request->region_value === SetRegionMW::REGION_UZBEKISTAN) {
            $currency = Transaction::CUR_USD;
        }

        if ($request->api_token) {
            $user = User::where('api_token', $request->api_token)->firstOrFail();
            $user->update([
                'confirmation_code' => $confirmation_code,
                'cur' => $currency,
                'email' => Str::lower($request->email),
                'lang' => Str::lower($request->lang),
                'name' => $request->login ? Str::lower($request->login) : Str::lower('user_' . $r),
                'password' => bcrypt($request->password),
                'token_updated_at' => Carbon::now(),
                'params' => [
                    'ig_logins' => []
                ],
                'roles' => []
            ]);
        } else {
            $user = User::create([
                'api_token' => User::getFreeToken(),
                'confirmation_code' => $confirmation_code,
                'cur' => $currency,
                'email' => Str::lower($request->email),
                'lang' => Str::lower($request->lang),
                'name' => $request->login ? Str::lower($request->login) : Str::lower('user_' . $r),
                'password' => bcrypt($request->password),
                'token_updated_at' => Carbon::now(),
                'params' => [
                    'ig_logins' => []
                ],
                'roles' => []
            ]);
        }

        $user->searchForParent(request('ref_code'));

        if (!$request->api_token || Str::upper($request->cur) === Transaction::CUR_UZS) {
            $user->setBasicPremiumStatus();
        }

        if (!App::environment('testing')) {
            try {
                Mail::to($user->email)->later(10, new WelcomeEmail(
                    $user->name,
                    $user->email,
                    $request->input('password'),
                    Str::lower($request->lang)
                ));

                if ($request->send_bonus) {
                    Mail::to($user->email)->later(10, new GuideEmail(
                        $user->email,
                        Str::lower($request->lang)
                    ));
                }
            } catch(Swift_TransportException $e) {
                \Log::error('Auth mail error');
                \Log::error($e->getMessage());
            }
        }

        return new ApiSuccess('ok', ['token' => $user->api_token]);
    }

    #[Group('auth')]
    #[Endpoint('confirm/{confirmationCode}')]
    #[Verbs(D::GET)]
    #[Text('Подтверждение регистрации')]
    #[Text('Данный метод отключен')]
    #[Param('confirmationCode', true, D::STRING, 'код подтверждение')]
    public function confirm($confirmation_code): ApiResponse
    {
        $confirmation_code ??
        throw (new MissingParameterException())
            ->withData(['parameter' => 'confirmation_code']);

        $user = User::where('confirmation_code', $confirmation_code)->first();
        $user ?? throw new InvalidCodeException();

        $user->addRole(UserRole::ROLE_VERIFIED);
        $user->confirmation_code = null;
        $user->save();

        return new ApiSuccess(__('s.account_verified'));
    }

    #[Group('auth')]
    #[Endpoint('reset')]
    #[Verbs(D::POST)]
    #[Text('Сброс пароля')]
    #[Text('Генерирует код восстановления')]
    #[Param('username', true, D::STRING, 'Не обязательно если заполнено поле email')]
    #[Param('email', true, D::STRING, 'Не обязательно если заполнено поле username')]
    public function reset(Request $request): ApiResponse
    {
        $request->validate([
            'username' => 'string|required_without:email',
            'email' => 'email|required_without:username',
            'origin' => 'string|required|url',
        ]);

        if ($request->username) {
            $username = Str::lower($request->username);
            $user = User::where('name', $username)->firstOrFail();
        } else {
            $user = User::where('email', $request->email)->firstOrFail();
        }

        $reset_code = Str::random(30);
        $user->update(['reset_code' => $reset_code]);

        Mail::to($user->email)
            ->send(new ResetEmail($reset_code, $request->origin, $user->lang));

        return new ApiSuccess(__('s.reset_code_sent'));
    }

    #[Group('auth')]
    #[Endpoint('set_password')]
    #[Verbs(D::POST)]
    #[Text('Установка нового пароля после сброса')]
    #[Text('Используется код восстановления из POST /api/reset')]
    #[Param('reset_code', true, D::STRING)]
    #[Param('password', true, D::STRING)]
    #[Param('password_confirm', true, D::STRING, '== password')]
    public function setPassword(Request $request): ApiResponse
    {
        $request->validate([
            'reset_code'       => 'required',
            'password'         => 'required',
            'password_confirm' => 'required|same:password',
        ]);

        $user = User::where('reset_code', $request->reset_code)->first();
        $user ?? throw new InvalidCodeException();

        $user->setPassword($request->password);
        $user->update([ 'reset_code' => null ]);

        return new ApiSuccess(__('s.password_changed'));
    }

    #[Group('auth')]
    #[Endpoint('update_password')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Обновление пароля')]
    #[Param('old_password', true, D::STRING)]
    #[Param('new_password', true, D::STRING)]
    #[Param('password_confirm', true, D::STRING, '== должен совпадать с новым паролем')]
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->old_password, $user->password)) {
            throw new InvalidPasswordException();
        }

        $user->setPassword($request->password);

        return new ApiSuccess(__('s.password_changed'), [
            'api_token' => $user->api_token,
        ]);
    }

    #[Group('auth')]
    #[Endpoint('update_avatar')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Text('Загрузка аватара')]
    #[Param('avatar', true, D::IMAGE)]
    public function updateAvatar(Request $request): ApiResponse
    {
        $request->validate([
            'avatar' => 'required | mimes:jpeg,jpg,png,webp|max:5000',
        ]);

        $cover = $request->file('avatar');
        $extension = $cover->getClientOriginalExtension();
        $path = 'users/'.$cover->getFilename().'.'.$extension;
        Storage::disk('public')->put($path, File::get($cover));

        $user = Auth::user();
        $user->avatar = $path;
        $user->save();

        return new ApiSuccess(__('s.avatar_changed'));
    }

    #[Endpoint('logout')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('auth')]
    #[Text('Выход из аккаунта')]
    public function logout(): ApiResponse
    {
        if (Auth::check()) {
            Auth::user()->deleteToken();
        }

        return new ApiSuccess(__('s.logout_ok'));
    }
}
