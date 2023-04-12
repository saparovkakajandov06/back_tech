<?php

namespace App\Services;

use App\Domain\Models\CompositeOrder;
use App\Role\UserRole;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAnalytics
{
    const ga_url = 'http://www.google-analytics.com/collect';

    protected array $domains;

    public function __construct()
    {
        $this->domains = config('services.ga.domains');
    }

    public function getOrderClientId(CompositeOrder $order): string
    {
        // Превращаем строку вида GA1.2.2344765243547.76546783945 в 2344765243547.76546783945
        if ( $order->session_id !== null && preg_match('/\d+\.\d+$/', $order->session_id, $matches) === 1) {
            // GA1.2.2344765243547.76546783945 в 2344765243547.76546783945
            return $matches[0];
        }

        // If we have not google client id in our database, return orderId
        return strval($order->id);
    }

    public function generateData(CompositeOrder $order)
    {
        if(!isset($order->params['origin']) || !$order->params['origin']) {
            return;
        }

        $origin = $order->params['origin'];

        $host = strtoupper(parse_url($origin)['host']);

        if (!array_key_exists($host, $this->domains)){
            return;
        }


        $GA_ID = $this->domains[$host];

        $cur = $order->params['cur'];

        $profit = $order->profit($cur);

        $orderId = $order->id;
        $userId = $order->user_id;

        $count          = $order->totalCountInAllChunks();
        $profitPerItem  = $profit / $count;
        $sessionId      = $this->getOrderClientId($order);

        // Передаем два события для отдельных учетов транзакций и заказов
        // У заказов передаем услуги дополнительно
        
        $transaction = [
            'v'   => 1,             // api version
            'tid' => $GA_ID,        // id аналитики
            'cd1' => $sessionId,    // id рекламной сессии
            'cid' => $sessionId,    // id рекламной сессии
            'uid' => $userId,       // id user'а
            't'   => 'transaction', // hit type
            'dl'  => $origin,       // url страницы заказа
            'ti'  => $orderId,      // номер заказа
            'tr'  => $profit,       // рентабельность за весь заказ
            'ni'  => 1,             // non-interaction hit
            'cu'  => $cur,          // валюта
            'cd4' => in_array(UserRole::ROLE_AUTO, $order->user->roles ?: []) ? "guest" : "authorized_user" // статус пользователя
        ];
        $this->send($transaction);

        $item = [
            'v'   => 1,                       // api version
            'tid' => $GA_ID,                  // id аналитики
            'cd1' => $sessionId,              // id рекламной сессии
            'cid' => $sessionId,              // id рекламной сессии
            't'   => 'item',                  // hit type
            'dl'  => $origin,                 // url страницы заказа
            'ti'  => $orderId,                // номер заказа
            'ic'  => $order->userService->id, // идентификатор услуги
            'in'  => $order->userService->tag,// название услуги
            'ip'  => $profitPerItem,          // доход с единцы товара (например: стоимость 1 лайка)
            'iq'  => $count,                  // количество товара (например: 100)
            'ni'  => 1,                       // non-interaction hit
            'cu'  => $cur                     // валюта
        ];
        $this->send($item);
    }
    
    public function collect($data): self {
		$hostKey = strtoupper(parse_url(request()->headers->get('referer'))['host']);

		if (!array_key_exists($hostKey, $this->domains)){
			return $this;
		}
			
		$GA_ID = $this->domains[$hostKey];
        $data['tid'] = $GA_ID;

        $this->send($data);
        return $this;
    }



    private function send($data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::ga_url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_exec($curl);
        curl_close($curl);
        //Http::post(self::ga_url, $data);
    }
}
