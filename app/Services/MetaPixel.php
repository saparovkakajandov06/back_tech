<?php

namespace App\Services;

use App\Domain\Models\CompositeOrder;
use App\Mail\CapiError;
use App\Mail\PixelErrorEmail;
use DateTime;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaPixel
{
    // https://graph.facebook.com/v13.0/{PIXEL_ID}/events

    protected array $domains;
    protected GoogleAnalytics $googleAnalytics;
    protected CurrencyService $currencyService;
    protected string $proxyAccess;
    protected bool $release;

    public function __construct(GoogleAnalytics $googleAnalytics, CurrencyService $currencyService)
    {
        $this->domains = config('services.metapixel.domains');
        $this->googleAnalytics = $googleAnalytics;
        $this->currencyService = $currencyService;
	$this->proxyAccess = config('app.FBA_PROXY');
	$this->release = config('app.FBA_RELEASE');
    }

    public function sendData(CompositeOrder $order)
    {
        if (!(array_key_exists('origin', $order->params) && $origin = $order->params['origin'] ?? false)) {
            return;
        }

        $host = strtoupper(parse_url($origin)['host']);

        if (!array_key_exists($host, $this->domains) || !$this->release){
            return;
        }

        $metaInfo = $this->domains[$host];
        $pixel_id = $metaInfo['pixel_id'];
        $pixel_currency = $metaInfo['currency'];
        $token = $metaInfo['access_token'];
        $cost = $order->params['cost'];
        $cur = $order->params['cur'];
        $item_price = $this->currencyService->convert(from: $cur, to: $pixel_currency, amount: $cost);

        $profit = $order->profit($pixel_currency);

        $timestamp = (new DateTime())->getTimestamp();
        $fbp = $order->params['fbp'];
        $fbc = $order->params['fbc'];
        $ua = $order->params['ua'];
        $ip = $order->params['ip'];
        $country = hash('sha256', Str::lower($order->params['country']));
        $external_id = hash('sha256', $this->googleAnalytics->getOrderClientId($order));
        $orderId = $order->id;
        $data = [
            [
                "event_name" => "Purchase",
                "event_id"=> $orderId,
                "event_time" => $timestamp,
                "user_data" => [
                    "external_id" => $external_id,
                    "country" => [ $country ],
                    "fbp" => $fbp,
                    "fbc" => $fbc,
                    "client_user_agent" => $ua,
                    "client_ip_address" => $ip,
                ],
                "custom_data" => [
                    "order_id" => strval($orderId),
                    "content_type" => "product",
                    "currency" => $pixel_currency,
                    "value" => round($profit,2),
                    "contents" => [
                        [
                            "quantity" => $order->params['count'],
                            "item_price" => round($item_price,2),
                            "id" => strval($order->userService->id),
                        ]
                    ]
                ],
                "event_source_url" => $origin,
                "action_source" => "website"
            ]
        ];


        $req = [
            'data' => json_encode($data),
            'access_token' => $token,
        ];

        if ($test_event_code = $metaInfo['test_event_code']) {
            $req['test_event_code'] = $test_event_code;
        }
	Log::info('[MP] order ' . $orderId . ' fb request:' . json_encode($req) );

	$requestOptions = [];

	if($this->proxyAccess){
		$requestOptions['proxy'] = $this->proxyAccess;
	}else{
		$requestOptions['force_ip_resolve'] = 'v6';
	}

	$res = Http::withOptions($requestOptions)
		->asForm()
		->post(
		"https://graph.facebook.com/v15.0/$pixel_id/events",
			$req
		);
	
       $jsonResponse = $res->json();
       if (!$res->successful() || array_key_exists('error', $jsonResponse)) {
           $emails = explode(',', config('logging.emails'));
           foreach ($emails as $email) {
               Mail::to($email)->send(new PixelErrorEmail($token, $data, $jsonResponse));
           }
       }
    }
}
