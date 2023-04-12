<?php

namespace App;

use App\Domain\Models\CompositeOrder;
use App\Domain\Models\Labels;
use App\Domain\Transformers\ITransformer;
use App\Domain\Validators\IValidator;
use App\Exceptions\NonReportable\NonReportableException;
use App\Exceptions\NonReportable\PipelineValidationException;
use App\Exceptions\Reportable\ReportableException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use US;

// услуга, которая продается пользователю

class UserService extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts
        = [
            'pipeline' => 'array',
            'runners' => 'array',
            'config' => 'array',
            'description' => 'array',
            'card' => 'array',
            'local_validation' => 'array',
            'labels' => 'array',
        ];

    protected $hidden
        = [
            'created_at',
            'updated_at',
//            'pipeline',
//            'runners',
            'local_validation',
            'local_checker',
//            'splitter',
//            'name',
//            'platform',
        ];

    const INSTAGRAM_LIKES_LK = 'INSTAGRAM_LIKES_LK';
    const INSTAGRAM_LIVE_LIKES_LK = 'INSTAGRAM_LIVE_LIKES_LK';
    const INSTAGRAM_SUBS_LK = 'INSTAGRAM_SUBS_LK';

    // light4
    const INSTAGRAM_LIKES_LIGHT4 = 'INSTAGRAM_LIKES_LIGHT4';
    const INSTAGRAM_SUBS_LIGHT4 = 'INSTAGRAM_SUBS_LIGHT4';
    const INSTAGRAM_VIEWS_VIDEO_LIGHT4 = 'INSTAGRAM_VIEWS_VIDEO_LIGHT4';

    const TIKTOK_LIKES_LIGHT4 = 'TIKTOK_LIKES_LIGHT4';
    const TIKTOK_SUBS_LIGHT4 = 'TIKTOK_SUBS_LIGHT4';
    const TIKTOK_VIEWS_LIGHT4 = 'TIKTOK_VIEWS_LIGHT4';

    // для главной страницы
    const INSTAGRAM_LIKES_MAIN = 'INSTAGRAM_LIKES_MAIN';
    const INSTAGRAM_SUBS_MAIN = 'INSTAGRAM_SUBS_MAIN';


    const INSTAGRAM_VIEWS_VIDEO_MAIN = 'INSTAGRAM_VIEWS_VIDEO_MAIN';
    //автолайки
    const INSTAGRAM_AUTO_LIKES_MAIN = 'INSTAGRAM_AUTO_LIKES_MAIN';
    //автолайки
    const INSTAGRAM_AUTO_VIEWS_MAIN = 'INSTAGRAM_AUTO_VIEWS_MAIN';
    //просмотры историй
    const INSTAGRAM_VIEWS_STORY_MAIN = 'INSTAGRAM_VIEWS_STORY_MAIN';
    // на последние посты
    const INSTAGRAM_MULTI_LIKES_MAIN = 'INSTAGRAM_MULTI_LIKES_MAIN';
    // просмотры reel
    const INSTAGRAM_VIEWS_REELS_MAIN = 'INSTAGRAM_VIEWS_REELS_MAIN';
    // просмотры igtv
    const INSTAGRAM_VIEWS_IGTV_MAIN = 'INSTAGRAM_VIEWS_IGTV_MAIN';
    // в прямой эфир
    const INSTAGRAM_VIEWS_LIVE_LK = 'INSTAGRAM_VIEWS_LIVE_LK';
    // Story
    const INSTAGRAM_VIEWS_STORY_LK = 'INSTAGRAM_VIEWS_STORY_LK';
    // показы+охват
    const INSTAGRAM_VIEWS_SHOWS_IMPRESSIONS_LK = 'INSTAGRAM_VIEWS_SHOWS_IMPRESSIONS_LK';
    // зрители в прямой эфир
    const INSTAGRAM_LIVE_VIEWERS_LK = 'INSTAGRAM_LIVE_VIEWERS_LK';
    // видео + охват
    const INSTAGRAM_VIEWS_VIDEO_LK = 'INSTAGRAM_VIEWS_VIDEO_LK';

    const INSTAGRAM_VIEWS_IGTV_LK = 'INSTAGRAM_VIEWS_IGTV_LK';
    // просмотры reels
    const INSTAGRAM_VIEWS_REELS_LK = 'INSTAGRAM_VIEWS_REELS_LK';

    const INSTAGRAM_AUTO_LIKES_LK = 'INSTAGRAM_AUTO_LIKES_LK';
    const INSTAGRAM_AUTO_LIKES_VIEWS_IMPRESSIONS_LK = 'INSTAGRAM_AUTO_LIKES_VIEWS_IMPRESSIONS_LK';
    const INSTAGRAM_AUTO_VIEWS_LK = 'INSTAGRAM_AUTO_VIEWS_LK';

    const INSTAGRAM_COMMENTS_LK = 'INSTAGRAM_COMMENTS_LK';
    const INSTAGRAM_COMMENTS_POSITIVE_LK = 'INSTAGRAM_COMMENTS_POSITIVE_LK';
    const INSTAGRAM_COMMENTS_CUSTOM_LK = 'INSTAGRAM_COMMENTS_CUSTOM_LK';

    const INSTAGRAM_MULTI_LIKES_LK = 'INSTAGRAM_MULTI_LIKES_LK';
    const INSTAGRAM_MULTI_COMMENTS_POSITIVE_LK = 'INSTAGRAM_MULTI_COMMENTS_POSITIVE_LK';
    const INSTAGRAM_MULTI_COMMENTS_CUSTOM_LK = 'INSTAGRAM_MULTI_COMMENTS_CUSTOM_LK';

    //youtube
    const YOUTUBE_LIKES_LK = 'YOUTUBE_LIKES_LK';
    const YOUTUBE_DISLIKES_LK = 'YOUTUBE_DISLIKES_LK';
    const YOUTUBE_VIEWS_LK = 'YOUTUBE_VIEWS_LK';
    const YOUTUBE_SUBS_LK = 'YOUTUBE_SUBS_LK';
    const YOUTUBE_VIEWS_SHORTS_LK = 'YOUTUBE_VIEWS_SHORTS_LK';
    const YOUTUBE_VIEWS_DURATION_LK = 'YOUTUBE_VIEWS_DURATION_LK';

    const YOUTUBE_LIKES_MAIN = 'YOUTUBE_LIKES_MAIN';
    const YOUTUBE_DISLIKES_MAIN = 'YOUTUBE_DISLIKES_MAIN';
    const YOUTUBE_VIEWS_MAIN = 'YOUTUBE_VIEWS_MAIN';
    const YOUTUBE_SUBS_MAIN = 'YOUTUBE_SUBS_MAIN';
    const YOUTUBE_VIEWS_DURATION_MAIN = 'YOUTUBE_VIEWS_DURATION_MAIN';

    //tiktok Главная
    const TIKTOK_LIKES_MAIN = "TIKTOK_LIKES_MAIN";
    const TIKTOK_SUBS_MAIN = "TIKTOK_SUBS_MAIN";
    const TIKTOK_VIEWS_MAIN = "TIKTOK_VIEWS_MAIN";
    const TIKTOK_AUTO_LIKES_MAIN = "TIKTOK_AUTO_LIKES_MAIN";
    const TIKTOK_AUTO_VIEWS_MAIN = "TIKTOK_AUTO_VIEWS_MAIN";
    const TIKTOK_REPOSTS_MAIN = "TIKTOK_REPOSTS_MAIN";

    //tiktok ЛК
    const TIKTOK_LIKES_LK = "TIKTOK_LIKES_LK";
    const TIKTOK_SUBS_LK = "TIKTOK_SUBS_LK";
    const TIKTOK_VIEWS_LK = "TIKTOK_VIEWS_LK";
    const TIKTOK_AUTO_LIKES_LK = "TIKTOK_AUTO_LIKES_LK";
    const TIKTOK_AUTO_VIEWS_LK = "TIKTOK_AUTO_VIEWS_LK";
    const TIKTOK_REPOSTS_LK = "TIKTOK_REPOSTS_LK";

    const TIKTOK_COMMENTS_POSITIVE_LK = 'TIKTOK_COMMENTS_POSITIVE_LK';
    const TIKTOK_COMMENTS_CUSTOM_LK = 'TIKTOK_COMMENTS_CUSTOM_LK';

    // VK Главная
    const VK_LIKES_MAIN = "VK_LIKES_MAIN";
    const VK_SUBS_MAIN = "VK_SUBS_MAIN";
    const VK_FRIENDS_MAIN = "VK_FRIENDS_MAIN";
    const VK_COMMENTS_MAIN = "VK_COMMENTS_MAIN";
    const VK_AUTO_LIKES_MAIN = "VK_AUTO_LIKES_MAIN";
    const VK_REPOSTS_MAIN = "VK_REPOSTS_MAIN";
    const VK_VIEWS_POST_MAIN = 'VK_VIEWS_POST_MAIN';
    const VK_VIEWS_VIDEO_MAIN = 'VK_VIEWS_VIDEO_MAIN';

    // VK ЛК
    const VK_LIKES_LK = 'VK_LIKES_LK';
    const VK_SUBS_LK = 'VK_SUBS_LK';
    const VK_FRIENDS_LK = 'VK_FRIENDS_LK';
    const VK_COMMENTS_LK = 'VK_COMMENTS_LK';
    const VK_AUTO_LIKES_LK = 'VK_AUTO_LIKES_LK';
    const VK_REPOSTS_LK = 'VK_REPOSTS_LK';
    const VK_VIEWS_POST_LK = 'VK_VIEWS_POST_LK';
    const VK_VIEWS_VIDEO_LK = 'VK_VIEWS_VIDEO_LK';

    // Telegram LK
    const TELEGRAM_VIEWS_LK = 'TELEGRAM_VIEWS_LK';
    const TELEGRAM_SUBS_LK = 'TELEGRAM_SUBS_LK';

    // Telegram MAIN
    const TELEGRAM_VIEWS_MAIN = 'TELEGRAM_VIEWS_MAIN';
    const TELEGRAM_SUBS_MAIN = 'TELEGRAM_SUBS_MAIN';

    const TEST_TEST = 'TEST_TEST';
    const TEST_TIME = 'TEST_TIME';
    const TEST_TEST_2 = 'TEST_TEST_2';
    const IG_LIKES_TEST = 'IG_LIKES_TEST';
    const FAKE_SERVICE_LK = 'FAKE_SERVICE_LK';
    const FAKE_SERVICE_MAIN = 'FAKE_SERVICE_MAIN';

    const GROUP_LIKES = 'GROUP_LIKES';
    const GROUP_VIEWS = 'GROUP_VIEWS';
    const GROUP_SUBS = 'GROUP_SUBS';
    const GROUP_COMMENTS = 'GROUP_COMMENTS';
    const GROUP_OTHER = 'GROUP_OTHER';


    public function compositeOrders()
    {
        return $this->hasMany(CompositeOrder::class);
    }

    public function price()
    {
        return $this->hasOne(USPrice::class, 'tag', 'tag');
    }

    public function processPipeline(array &$params): self
    {
        foreach ($this->pipeline as $clazz) {
            $pipelineElement = App::make($clazz);
            if ($pipelineElement instanceof IValidator) {
                $valRes = $pipelineElement->validate($params);
                if (!$valRes->isValid) {
                    $msg = $clazz . ': ' . $valRes->message;
                    throw (new PipelineValidationException($msg))
                                    ->withData($valRes->data);
                }
            } elseif ($pipelineElement instanceof ITransformer) {
                $params = $pipelineElement->transform($params, $this);
            }
        }

        return $this;
    }

    public function processLocalValidation(array $params): self
    {
        foreach ($this->local_validation as $clazz) {
            $validator = App::make($clazz);
            $valRes = $validator->validate($params);
            if (!$valRes->isValid) {
                $msg = $clazz.': '.$valRes->message;
                throw (new PipelineValidationException($msg))
                            ->withData($valRes->data);
            }
        }

        return $this;
    }

    public function check(Action $action): bool
    {
        $checker = App::make($this->local_checker);
        $valRes = $checker->validate($action);

        return $valRes->isValid;
    }

    // make chunks
    public function split(CompositeOrder $order): array
    {
        $splitterClass = $this->splitter;
        $splitter = App::make($splitterClass);
        $distribution = $splitter->split($order, $this->config);

        return $distribution;
    }

    // стоимость одного лайка/просмотра, учитывая количество и валюту
    public function getPrice($n, $cur='RUB')
    {
        // get from prices table
        if(! $this->price) {
            throw new ReportableException(__('exceptions.no_price', [
                'service' => $this->tag,
            ]));
        }

        $list = $this->price[$cur];

        if(! $list) {
            throw new ReportableException(__('exceptions.no_price_in_cur', [
                'service' => $this->tag,
                'cur' => $cur,
            ]));
        }

        ksort($list);
        $price = $list[1];

        foreach ($list as $k => $v) {
            if ($n < $k) {
                break;
            }
            $price = $v;
        }

        return $price;
    }

    private function findDiscount(): ?string
    {
        return collect($this->labels)
                ->first(fn($label) => str_contains($label, 'DISCOUNT'));
    }

    // стоимость с учетом программы лояльности
    public function getFinalCost($n, $cur): float
    {
        $user = Auth::user();
        [ $cost, $discount ] = $this->getFinalCostAndDiscount($n, $cur, $user);

        return $cost;
    }

    protected function premiumStatusDiscountPercent(?User $user): float
    {
        if (! $user) return 0.0;

        $type = $this->findDiscount();
        $discount = $user->premiumStatus->discount;
        $discountPercent = $discount[$type] ??
                           $discount[Labels::DISCOUNT_BASIC];

        return $discountPercent;
    }

    public static function getPricesFromOrders(array $orders, string $cur,  $user = null) : array
    {
        $tags = [];

        collect($orders)->each(function (CompositeOrder $order) use (&$tags) {
            if (!isset($tags[$order->user_service_id]['count'])) {
                $tags[$order->user_service_id]['count'] = $order->params['count'];
                return;
            }
            $tags[$order->user_service_id]['count'] += $order->params['count'];
        });

        collect($tags)->each(function($tag, $tagId) use ($user, $cur, &$tags) {
            $f = UserService::find($tagId)->getFinalCostAndDiscount($tag['count'], $cur, $user);
            $tags[$tagId]['price'] = $f[0];
            $tags[$tagId]['cashback'] = $f[2];
        });

        return $tags;
    }

    // новая логика стоимости с учетом количества и программы лояльности
    public function getFinalCostAndDiscount($n, $cur, $user = null): array
    {
        // price - price for one item
        // cost - for many
        $baseCost = $this->getPrice(1, $cur) * $n;
        $costFromCount = $this->getPrice($n, $cur) * $n;
        // 0 or greater
        $p = $this->premiumStatusDiscountPercent($user);

        $finalCost = round($costFromCount, 2, PHP_ROUND_HALF_DOWN);
        $discount = round($baseCost - $finalCost, 2, PHP_ROUND_HALF_UP);

        $costFromPS = $finalCost * $p * 0.01;

        return [ $finalCost, abs($discount), $costFromPS ];
    }

    public static function getPipeline(string $tag): array
    {
        return self::where('tag', $tag)->firstOrFail()->pipeline;
    }

    public static function tag($tag)
    {
        return self::where('tag', $tag)->firstOrFail();
    }

    public function hasLabel($label): bool
    {
        return collect($this->labels)->contains($label);
    }

    public function addLabel($label): array
    {
        if (! $this->hasLabel($label)) {
            $this->update([
                'labels' => $this->labels ?
                     [ ...$this->labels, $label ] :
                     [ $label ]
            ]);
        }

        return $this->labels;
    }

    public function removeLabel($label): array
    {
        $this->update([
            'labels' => collect($this->labels)
                        ->filter(fn($lab) => $lab !== $label)
                        ->all()
        ]);

        return $this->labels;
    }

    public function replaceLabel($from, $to): array
    {
        $labels = collect($this->labels);
        if (! $labels->contains($from)) {
            throw new NonReportableException("Label $from not found.");
        }

        $newLabels = $labels->map(
          fn($label) => $label === $from ? $to : $label);

        $this->labels = $newLabels;
        $this->save();

        return $this->labels;
    }

    public function scopeWithLabel($query, $label)
    {
        return $query->where('labels', '?', $label);
    }

    public static function findByTagCached(string $tag): ?static
    {
        $key = 'find_user_service_by_tag_' . $tag;
        try {
            $us = Cache::remember($key, now()->addMinutes(5), fn() =>
                serialize(US::where('tag', $tag)->firstOrFail())
            );
            return unserialize($us);
        }
        catch (\Throwable $e) {
            return null;
        }
    }

    public static function tagsCached(?string $platform, ?string $labels)
    {
        $key = 'service_tags';
        $q = US::query();
        if ($platform) {
            $q->where('platform', ucfirst(strtolower($platform)));
            $key .= '_'.strtoupper($platform);
        }

        if ($labels) {
            Str::of($labels)
                ->upper()
                ->explode(' ')
                ->each(function($label) use (&$key, &$q) {
                    $q->withLabel($label);
                    $key .= '+'.$label;
                });
        }
        try {
            $us = Cache::remember($key, now()->addMinutes(5), fn() =>
                serialize($q->get('tag'))
            );
            return unserialize($us);
        }
        catch (\Throwable $e) {
            return null;
        }
    }

    public static function tinyCached(?string $platform, ?string $labels)
    {
        $key = 'tiny_services';
        $q = US::query();
        if ($platform) {
            $q->where('platform', ucfirst(strtolower($platform)));
            $key .= '_'.strtoupper($platform);
        }

        if ($labels) {
            Str::of($labels)
                ->upper()
                ->explode(' ')
                ->each(function($label) use (&$key, &$q) {
                    $q->withLabel($label);
                    $key .= '+'.$label;
                });
        }
        try {
            $us = Cache::remember($key, now()->addMinutes(5), fn() =>
                serialize(
                    $q->orderBy('id')->select(
                        'config',
                        'img',
                        'labels',
                        'max_order',
                        'min_order',
                        'order_frequency',
                        'order_speed',
                        'platform',
                        'tag'
                    )->get()->map(function($us) {
                        $min = [];
                        $max = 0;
                        $config = $us->config;
                        array_walk($config, function($value, $key) use (&$min, &$max) {
                            if (isset($value['isEnabled']) && !$value['isEnabled']) {
                                return;
                            }
                            $min = min($min, $value['min']);
                            $max += $value['max'];
                        });
                        return [
                            'img'             => $us->img,
                            'labels'          => $us->labels,
                            'max'             => $max,
                            'max_order'       => $us->max_order,
                            'min'             => $min,
                            'min_order'       => $us->min_order,
                            'order_frequency' => $us->order_frequency,
                            'order_speed'     => $us->order_speed,
                            'platform'        => $us->platform,
                            'tag'             => $us->tag,
                        ];
                    })
                )
            );
            return unserialize($us);
        }
        catch (\Throwable $e) {
            return null;
        }
    }
}
