<?php

namespace App\Services;

use App\Domain\Models\CompositeOrder;
use App\EventsScheme;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SendpulseService
{
    const AUTH_URL = 'https://api.sendpulse.com/oauth/access_token';
    private $clientId;
    private $clientSecret;
    private $carbonNow;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->carbonNow = Carbon::now()->tz('Europe/Moscow')->format('Y-m-d H:i');
    }

    private function getAccessToken(): string
    {
        $res = Http::post(self::AUTH_URL, [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ]);
        if($res->successful()){
            $res = $res->json('access_token');
        } else {
            $res = null;
        }

        return $res;

    }
    public function httpWrapper(string $url, array $params)
    {
        $token = $this->getAccessToken();
        if(!$token){
            return false;
        }
        $res = Http::withToken($token)
            ->post($url, $params);

        return $res->successful();
    }

    public function preparingUnpaidOrderData(EventsScheme $es)
    {
        $userId = $es->user_id;
        $user = User::find($userId);
        $tags = [];

        foreach($es->data['*']['tag'] as $tag){
            $tags[] = $this->getUslugaName($tag, $user->lang);
        }

        return $params = [
            'email' => $user?->email,
            'user_id' => $userId,
            'event_date' => $this->carbonNow,
            'user_location' => $user?->lang,
            'tip_uslugi_na_sayte' => implode(', ', array_unique($tags)),
            'account' => $user?->name,
            'usluga_value' => array_sum($es->data['*']['cost']) . ' ' . $user->cur,
            'user_url_ref' => 'https://smmtouch.tech?ref=' . $user?->ref_code,
        ];
    }

    public function sendUnpaidOrder(EventsScheme $es): bool
    {
        $url = 'https://events.sendpulse.com/events/name/smmtouch_tech_ne_oplatil_zakaza';

        $params = $this->preparingUnpaidOrderData($es);

        $canCreate = $this->eventToCache(EventsScheme::UNPAID_ORDER, $es->user_id);
        if(!$canCreate){
            return false;
        }

        return $this->httpWrapper($url, $params);
    }

    public function preparingSuccededPaymentData(EventsScheme $es)
    {
        $userId = $es->user_id;
        $user = User::find($userId);

        $tags = $this->getUserServiceName($es->data['order_ids'], $user?->lang);

        return $params = [
            'email' => $user?->email,
            'user_id' => $userId,
            'event_date' => $this->carbonNow,
            'user_location' => $user?->lang,
            'tip_uslugi_na_sayte' => $tags,
            'account' => $user?->name,
            'usluga_value' => (string) abs($es->data['amount']) . ' ' . $es->data['cur'],
            'user_url_ref' => 'https://smmtouch.tech?ref=' . $user?->ref_code
        ];
    }

    public function sendSuccededPayment(EventsScheme $es): bool
    {
        $url = 'https://events.sendpulse.com/events/name/smmtouch_tech_spasibo_za_zakaz';

        $params = $this->preparingSuccededPaymentData($es);

        $canCreate = $this->eventToCache(EventsScheme::SUCCEDED_PAYMENT, $es->user_id);
        if(!$canCreate){
            return false;
        }

        return $this->httpWrapper($url, $params);
    }

    public function preparingNotTopUpBalanceData(EventsScheme $es)
    {
        $userId = $es->user_id;
        $user = User::find($userId);

        return $params = [
            'email' => $user?->email,
            'user_id' => $userId,
            'event_date' => $this->carbonNow,
            'user_location' => $user?->lang,
            'tip_uslugi_na_sayte' => 'lk_popolnenie',
            'account' => $user?->name,
            'usluga_value' => (string) $es->data . ' ' . $user?->cur,
            'user_url_ref' => 'https://smmtouch.tech?ref=' . $user?->ref_code
        ];
    }

    public function sendNotTopUpBalance(EventsScheme $es): bool
    {
        $url = 'https://events.sendpulse.com/events/name/smmtouch_tech_ne_oformil_zakaz';

        $params = $this->preparingNotTopUpBalanceData($es);

        $canCreate = $this->eventToCache(EventsScheme::NOT_TOP_UP_BALANCE, $es->user_id);
        if(!$canCreate){
            return false;
        }

        return $this->httpWrapper($url, $params);
    }

    public function preparingAbandonedCart(EventsScheme $es)
    {
        $userId = $es->user_id;
        $user = User::find($userId);

        foreach($es->data['*']['tag'] as $tag){
            $tags[] = $this->getUslugaName($tag, $user->lang);
        }

        return $params = [
            'email' => $user?->email,
            'user_id' => $userId,
            'event_date' => $this->carbonNow,
            'user_location' => $user?->lang,
            'account' => $user?->name,
            'tip_uslugi_na_sayte' => implode(', ', array_unique($tags)),
            'usluga_value' => (string) array_sum($es->data['*']['cost']) . ' ' . $user?->cur,
            'user_url_ref' => 'https://smmtouch.tech?ref=' . $user?->ref_code
        ];
    }

    public function sendAbandonedСart(EventsScheme $es): bool
    {
        $url = 'https://events.sendpulse.com/events/name/broshennaya_korzina';

        $params = $this->preparingAbandonedCart($es);

        $canCreate = $this->eventToCache(EventsScheme::ABANDONED_CART, $es->user_id);
        if(!$canCreate){
            return false;
        }

        return $this->httpWrapper($url, $params);
    }

    public function getUserServiceName($orderIds, $lang)
    {
        if(empty($orderIds)){
            return null;
        }
        foreach($orderIds as $orderId){
            $order = CompositeOrder::find($orderId);
            $tag = $order?->userservice?->tag ?: 'NO_INFO';
            $servicesNames[] = $this->getUslugaName($tag, $lang);
        }

        return implode(', ', array_unique($servicesNames));
    }

    public function getUslugaName($tag, $lang)
    {
        $tipUslugiNaSayte = (__(
            key: 'userservices.' . $tag,
            locale: $lang
        ));
        return (string) $tipUslugiNaSayte;
    }

    public function eventToCache($eventType, $userId)
    {
        $key = $userId . $eventType;

        if (Cache::has($key)) {
            return false;
        }

        return Cache::put($key, 'true', 3600 * 24);
    }
}

?>