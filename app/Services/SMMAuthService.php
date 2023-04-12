<?php

namespace App\Services;

use App\Exceptions\Reportable\BadCredentialsException;
use App\Mail\WelcomeEmail;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SMMAuthService
{
    private function useFbLoginHttpClient($url, $params = [])
    {
        return Http::useProxy()->get($url, $params);
    }

    private function getFreeLoginFromEmail(string $email): string
    {
        $name = explode('@', $email)[0];
        $freeName = $name;
        $number = 0;
        while (User::where('name', $freeName)->exists()) {
            $freeName = $name . $number++;
        }
        return $freeName;
    }

    private function getVkKey(string $user_token): string
    {
        return 'VK_ACCESS_TOKEN_' . $user_token;
    }

    public function localLogin($emailOrLogin, $password)
    {
        $emailOrLogin = strtolower($emailOrLogin);
        if (Auth::attempt([
            'email'    => $emailOrLogin,
            'password' => $password
        ]) or Auth::attempt([
            'name'     => $emailOrLogin,
            'password' => $password
        ])) {
            return Auth::user()->updateToken();
        }
        return '';
    }

    /**
     * @param array $data
     * @throws BadCredentialsException
     * @deprecated
     */
    protected function validateNameAndEmail(array $data)
    {
        if (!$name = Arr::get($data, 'nickname')) {
            throw new BadCredentialsException('no name field');
        }
        if (!$email = Arr::get($data, 'email')) {
            throw new BadCredentialsException('no email field');
        }
        if(User::where('name', strtolower($name))->exists()) {
            throw new BadCredentialsException('name already taken');
        }
        if(User::where('email', strtolower($email))->exists()) {
            throw new BadCredentialsException('email already taken');
        }
    }

    public function confirmFacebookToken ($input_token, $user_id): bool
    {
        $client_id = config('services.fb_login.client_id');
        $secret = config('services.fb_login.secret');
        $access_token = "$client_id|$secret";

        $response = $this->useFbLoginHttpClient(
            'https://graph.facebook.com/debug_token',
            [
                'input_token'  => $input_token,
                'access_token' => $access_token,
            ]
        )->json();

        return (Arr::get($response, 'data.is_valid') && Arr::get($response, 'data.user_id') === $user_id);
    }

    public function facebookLogin(array $data): string
    {
        if ($user = User::where('social_id', Arr::get($data, 'id'))->first()) {
            return $user->updateToken();
        }
        if (!$email = Arr::has($data, 'email') ? Str::lower(Arr::get($data, 'email')) : false) {
            return '';
        }
        if ($user = User::where('email', 'ILIKE', $email)->first()) {
            $user->social_id = Arr::get($data, 'id');
            $user->network = 'facebook';
            $user->save();
            return $user->updateToken();
        }
        if (!$lang = Str::lower(Arr::get($data, 'lang'))) {
            return '';
        }
        if (!$cur = Str::upper(Arr::get($data, 'cur'))) {
            return '';
        }
        $name = $this->getFreeLoginFromEmail($email);
        $saveData = [
            'avatar'    => Arr::get($data, 'picture'),
            'cur'       => $cur,
            'email'     => $email,
            'lang'      => $lang,
            'name'      => $name,
            'network'   => 'facebook',
            'params'    => [ 'ig_logins' => [] ],
            'roles' => [],
            'social_id' => Arr::get($data, 'id'),
        ];
        $user = User::create($saveData);
        $user->searchForParent(Arr::get($data, 'ref_code'));
        $user->setBasicPremiumStatus();
        $password = Str::random(8);
        $user->password = bcrypt($password);
        $user->save();

        if (!\App::environment('testing')) {
            Mail::to($email)->later(10, new WelcomeEmail($name, $email, $password, $lang));
        }
        return $user->updateToken();
    }

    public function facebookLoginWithConfirm(array $data): string
    {
        if (!$email = Str::lower(Arr::get($data, 'email'))) {
            return '';
        }
        if (!Arr::has($data, 'confirm')) {
            return '';
        }
        if (!Arr::has($data, 'id')) {
            return '';
        }
        if (!$user = User::where('email', 'ILIKE', $email)->first()) {
            return '';
        }
        if (!Auth::attempt(['email' => $email, 'password' => Arr::get($data, 'confirm')])) {
            return '';
        }
        $user->social_id = Arr::get($data, 'id');
        $user->network = 'facebook';
        $user->save();
        return $user->updateToken();
    }

    public function getVKToken(string $code, string $user_token): array
    {
        $client_id = config('services.vk_login.client_id');
        $secret = config('services.vk_login.secret');
        $url = config('app.url') . "/api/login/vk/callback?token=$user_token";

        $response = Http::get('https://oauth.vk.com/access_token', [
            'client_id'     => $client_id,
            'client_secret' => $secret,
            'code'          => $code,
            'redirect_uri'  => $url,
        ])->json();

        return $response;
    }

    public function getVKUserAndSave(array $data, string $user_token): void
    {
        $url = 'https://api.vk.com/method/users.get';
        $response = Http::get($url, [
            'access_token' => Arr::get($data, 'access_token'),
            'fields'       => 'has_photo, photo_400_orig,nickname',
            'user_ids'     => Arr::get($data, 'user_id'),
            'v'            => '5.131',
        ])->json();

        $key = $this->getVkKey($user_token);
        $user = array_merge($data, Arr::get($response, 'response.0'));

        Cache::put($key, $user, now()->addHour());
    }

    public function vkLogin(array $login_data): array
    {
        $user_token = Arr::get($login_data, 'token');
        $key = $this->getVkKey($user_token);
        $cache_data = Cache::get($key);

        if (!$cache_data) {
            return ['status' => 'error'];
        }

        $data = array_merge($login_data, $cache_data);

        if (!$data) {
            return ['status' => 'error'];
        }
        if ($user = User::where('social_id', Arr::get($data, 'user_id'))->first()) {
            return ['status' => 'ok', 'token' => $user->updateToken()];
        }
        if (!$email = Arr::has($data, 'email') ? Str::lower(Arr::get($data, 'email')) : false) {
            return ['status' => 'error', 'data' => 'no-email'];
        }
        if ($user = User::where('email', 'ILIKE', $email)->first()) {
            $user->social_id = Arr::get($data, 'user_id');
            $user->network = 'vkontakte';
            $user->save();
            Cache::forget($key);
            return ['status' => 'ok', 'token' => $user->updateToken()];
        }
        if (!$lang = Str::lower(Arr::get($login_data, 'lang'))) {
            return ['status' => 'error'];
        }
        if (!$cur = Str::upper(Arr::get($login_data, 'cur'))) {
            return ['status' => 'error'];
        }
        $name = $this->getFreeLoginFromEmail($email);
        $avatar = Arr::has($data, 'has_photo') ? Arr::get($data, 'photo_400_orig') : null;
        $saveData = [
            'avatar'    => $avatar,
            'cur'       => $cur,
            'email'     => $email,
            'lang'      => $lang,
            'name'      => $name,
            'network'   => 'vkontakte',
            'params'    => [ 'ig_logins' => [] ],
            'roles'     => [],
            'social_id' => Arr::get($data, 'user_id'),
        ];
        $user = User::create($saveData);
        $user->searchForParent(Arr::get($data, 'ref_code'));
        $user->setBasicPremiumStatus();
        $password = Str::random(8);
        $user->password = bcrypt($password);
        $user->save();

        if (!\App::environment('testing')) {
            Mail::to($email)->later(10, new WelcomeEmail($name, $email, $password, $lang));
        }
        Cache::forget($key);
        return ['status' => 'ok', 'token' => $user->updateToken()];
    }

    public function vkLoginWithConfirm(array $login_data): string
    {
        $user_token = Arr::get($login_data, 'token');
        $key = $this->getVkKey($user_token);
        $cache_data = Cache::get($key);
        $data = array_merge($login_data, $cache_data);

        if (!$data) {
            return '';
        }
        if (!$email = Str::lower(Arr::get($data, 'email'))) {
            return '';
        }
        if (!Arr::has($data, 'confirm')) {
            return '';
        }
        if (!Arr::has($data, 'user_id')) {
            return '';
        }
        if (!$user = User::where('email', 'ILIKE', $email)->first()) {
            return '';
        }
        if (!Auth::attempt(['email' => $email, 'password' => Arr::get($data, 'confirm')])) {
            return '';
        }
        $user->social_id = Arr::get($data, 'user_id');
        $user->network = 'vkontakte';
        $user->save();
        Cache::forget($key);
        return $user->updateToken();
    }
}
