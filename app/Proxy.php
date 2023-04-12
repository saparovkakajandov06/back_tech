<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Proxy extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function requests()
    {
        return $this->hasMany(ProxyRequest::class);
    }

    public function responses()
    {
        return $this->hasMany(ProxyResponse::class);
    }

    public static function getRandom($bad=[], $ig=false)
    {
        $q = Proxy::where('enabled', true)
                    ->whereNotIn('id', $bad);

        if ($ig) {
            $q->whereNotNull('instagram');
        }

        return $q->inRandomOrder()
                 ->firstOrFail();
    }

    // proxy without credentials
    public function getProxyAttribute()
    {
        //"http://gjTEVr:P39aPS@45.10.80.67:8000";
        $url = $this->getAttribute('url');

        return explode('@', $url)[1];
    }

    private static function getRate($proxy, $minutes = 0xFFFFFFFF): array
    {
        $responses = $proxy
            ->responses
            ->where('created_at', '>', Carbon::parse("$minutes minutes ago"));

        $nRes = $responses->count();
        $nSuccess = $responses->where('success', true)->count();

        if ($nSuccess === $nRes) {
            $rate = 1.0;
        } elseif ($nRes) {
            $rate = $nSuccess * 1.00 / $nRes;
        } else {
            $rate = 0;
        }

        return [$nRes, $nSuccess, $rate];
    }

    public static function list($offset, $limit)
    {
        return Proxy::with(['responses'])
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($p) {
                $p->rate10 = self::getRate($p, 10);
                $p->rate120 = self::getRate($p, 120);
                $p->rateTotal = self::getRate($p);

                unset($p->responses);

                return $p;
            });
    }

    public static function available($minutes, $minRate)
    {
        $available = 0;

        Proxy::with(['responses'])
            ->get()
            ->each(function ($proxy) use ($minutes, $minRate, &$available) {
                [$total, $success, $rate] = self::getRate($proxy, $minutes);
//                echo "current rate $rate\n";
                if ($rate >= $minRate) {
//                    echo "yes\n";
                    $available++;
                } else {
//                    echo "no\n";
                }
            });

        return $available;
    }

    public static function getRequest(string $url, array $params, bool $ig=false, $attempts=2)
    {
        $bad = [];
        $usedProxies = [];
        $response = null;
        $a = 0;

        while(true) {
            $a++;
            $proxy = Proxy::getRandom($bad, $ig);
            $usedProxies[] = $proxy;

            $params = array_merge($params, [
                'proxy' => $proxy->url,
                'cookie' => $proxy->cookie,
                'user_agent' => $proxy->user_agent,
            ]);

            $proxy->requests()->create([ 'params' => $params ]);
            $response = Http::get($url, $params)->json();

            $success = ! data_get($response, 'error');

            $proxy->responses()->create([
                'params' => $response,
                'success' => $success,
            ]);

            if ($success or $a === $attempts) {
                break;
            } else {
                $bad[] = $proxy->id;
            }
        }

        $response['debug'] = [
            'used_proxies' => array_map(
                   fn($p) => $p->only('id', 'comment', 'proxy'),
                   $usedProxies
            ),
            'attempt' => $a,
        ];

        return $response;
    }
}
