<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Responses\ApiSuccess;
use App\Scraper\Models\IgMedia;
use App\Scraper\Models\IgUser;
use App\Scraper\Models\TTUser;
use App\Scraper\Models\YoutubeVideo;
use App\Scraper\PInstagramScraper;
use App\Scraper\Simple\BestExperienceTiktokScraper;
use App\Scraper\Simple\Instagram28Scraper;
use App\Scraper\Simple\InstagramBoboScraper;
use App\Scraper\Simple\Instagram12Scraper;
use App\Scraper\Simple\JoTiktokScraper;
use App\Scraper\Simple\KirtanTiktokScraper;
use App\Scraper\Simple\PremiumIgScraper;
use App\Scraper\Simple\Instagram39Scraper;
use App\Scraper\Simple\TelegramScraper;
use App\Scraper\Simple\VkApiScraper;
use App\Scraper\TiktokScraper;
use App\Scraper\YoutubeScraper;
use App\Services\LoginStats;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScraperController extends Controller
{

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/list_for_app')]
    #[Verbs(D::GET)]
    #[Text('Получение списка приоритетности скрэпперов для мобильного приложения')]
    public function listForApp(Request $request){	
		return [
			'instagram' => config('scrapers.instagram.list_for_app')
		];
	}
    #[Group('scraper')]
    #[Endpoint('scrape/instagram/profile/micro')]
    #[Verbs(D::GET)]
    #[Text('Получение данных от скрейпера по Instagram')]
    #[Param('login', true, D::URL)]
    public function microIgProfile(Request $request)
    {
        $request->validate([
            'login' => 'required',
        ]);

        $scraper = resolve(PInstagramScraper::class);
        $user = IgUser::fromLogin($request->login, $scraper);

        return [
            'user' => $user,
            'scraper' => get_class($scraper),
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/media/micro')]
    #[Verbs(D::GET)]
    #[Text('Получить данные от скрейпера по Instagram')]
    #[Param('link', true, D::URL)]
    public function microIgMedia(Request $request)
    {
        $request->validate([
            'link' => 'required',
        ]);

        $scraper = resolve(PInstagramScraper::class);
        $media = IgMedia::fromUrl($request->link, $scraper);

        return [
            'media' => $media,
            'scraper' => get_class($scraper),
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/feed/micro')]
    #[Verbs(D::GET)]
    #[Text('Получение данных от скрейпера по Instagram')]
    #[Param('login', true, D::STRING, 'может быть как логин так и ссылку на страницу')]
    #[Param('count', true, D::INT)]
    public function microIgFeed(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'count' => 'required|integer|min:1|max:100'
        ]);

        $scraper = resolve(PInstagramScraper::class);
        $posts = IgMedia::fromLogin($request->login, $request->count, $scraper);
        return [
            'posts' => $posts,
            'urls' => array_map(fn($p) => $p->link, $posts),
            'total' => count($posts),
            'scraper' => get_class($scraper),
        ];
    }

    // --------------- https://rapidapi.com/premium-apis-premium-apis-default/api/instagram85/ --------------
    #[Group('scraper')]
    #[Endpoint('scrape/instagram/profile/rapid85')]
    #[Verbs(D::GET)]
    #[Text('Данные профиля Instagram')]
    #[Param('login', true, D::STRING, 'логин', 'putin.life')]
    public function rapid85IgProfile(Request $request)
    {
        $val = $request->validate([ 'login' => 'required' ]);
        return new ApiSuccess('Instagram profile', [
            'sdata' => resolve(PremiumIgScraper::class)->profile($val['login']),
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/media/rapid85')]
    #[Verbs(D::GET)]
    #[Text('Данные медиа(фото/видео) Instagram')]
    #[Param('url', true, D::URL, 'ссылка на фото', 'https://www.instagram.com/p/CXJR5eOMXFV/')]
    public function rapid85IgMedia(Request $request)
    {
        $val = $request->validate([ 'url' => 'required|url' ]);
        return new ApiSuccess('Instagram media', [
            'sdata' => resolve(PremiumIgScraper::class)->media($val['url']),
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/feed/rapid85')]
    #[Verbs(D::GET)]
    #[Text('Лента пользователя Instagram')]
    #[Param('login', true, D::STRING, 'логин')]
    #[Param('posts', false, D::INT, 'кол-во постов, если не заполнено то = 12')]
    public function rapid85IgFeed(Request $request)
    {
        $val = $request->validate([
            'login' => 'required',
            'posts' => 'integer|min:1|max:100'
        ]);
        $data = resolve(PremiumIgScraper::class)->feed($val['login'], $val['posts'] ?? 12);
        return new ApiSuccess('Instagram feed', [
            'sdata' => $data,
            'count' => count($data),
        ]);
    }

    // --------------- https://rapidapi.com/socialminer/api/instagram39/ --------------
    #[Group('scraper')]
    #[Endpoint('scrape/instagram/profile/rapid39')]
    #[Verbs(D::GET)]
    #[Text('Данные профиля Instagram')]
    #[Param('login', true, D::STRING, 'логин', 'putin.life')]
    public function rapid39IgProfile(Request $request)
    {
        $val = $request->validate([ 'login' => 'required' ]);
        return new ApiSuccess('Instagram profile', [
            'sdata' => resolve(Instagram39Scraper::class)->profile($val['login']),
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/media/rapid39')]
    #[Verbs(D::GET)]
    #[Text('Данные медиа(фото/видео) Instagram')]
    #[Param('code', true, D::STRING, 'хеш-код публикации', 'CXJR5eOMXFV')]
    public function rapid39IgMedia(Request $request)
    {
        $val = $request->validate([ 'code' => 'required' ]);

        return new ApiSuccess('Instagram media', [
            'sdata' => resolve(Instagram39Scraper::class)->media($val['code']),
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/feed/rapid39')]
    #[Verbs(D::GET)]
    #[Text('Лента пользователя Instagram')]
    #[Param('login', true, D::STRING, 'логин')]
    #[Param('id', false, D::INT, 'id пользователя')]
    #[Param('posts', false, D::INT, 'кол-во постов, если не заполнено то = 12')]
    public function rapid39IgFeed(Request $request)
    {
        $val = $request->validate([
            'id' => 'required_without:login',
            'login' => 'required_without:id',
            'posts' => 'integer|min:1|max:100'
        ]);

        if (isset($val['id'])) {
            $data = resolve(Instagram39Scraper::class)->feedById($val['id'], $val['posts'] ?? 12);
        } else {
            $data = resolve(Instagram39Scraper::class)->feed($val['login'], $val['posts'] ?? 12);
        }

        return new ApiSuccess('Instagram feed', [
            'sdata' => $data,
            'count' => count($data),
        ]);
    }

    // --------------- https://rapidapi.com/yuananf/api/instagram28/ --------------
    #[Group('scraper')]
    #[Endpoint('scrape/instagram/profile/rapid28')]
    #[Verbs(D::GET)]
    #[Text('Данные профиля Instagram')]
    #[Param('login', true, D::STRING, 'логин', 'putin.life')]
    public function rapid28IgProfile(Request $request, LoginStats $stats)
    {
        $val = $request->validate([ 'login' => 'required' ]);
        $scraper = Instagram28Scraper::class;
        $stats->put($request, $scraper);
        $sdata = resolve($scraper)->profile($val['login']);
        $stats->put($request, $scraper, true);
        return new ApiSuccess('Instagram profile', [
            'sdata' => $sdata,
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/media/rapid28')]
    #[Verbs(D::GET)]
    #[Text('Данные медиа(фото/видео) Instagram')]
    #[Param('code', true, D::STRING, 'хеш-код публикации', 'CXJR5eOMXFV')]
    public function rapid28IgMedia(Request $request)
    {
        $val = $request->validate([ 'code' => 'required' ]);

        return new ApiSuccess('Instagram media', [
            'sdata' => resolve(Instagram28Scraper::class)->media($val['code']),
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/instagram/feed/rapid28')]
    #[Verbs(D::GET)]
    #[Text('Лента пользователя Instagram')]
    #[Param('login', true, D::STRING, 'логин')]
    #[Param('id', false, D::INT, 'id пользователя')]
    #[Param('posts', false, D::INT, 'кол-во постов, если не заполнено то = 12')]
    public function rapid28IgFeed(Request $request)
    {
        $val = $request->validate([
            'id' => 'required_without:login',
            'login' => 'required_without:id',
            'posts' => 'integer|min:1'
        ]);

        if (isset($val['id'])) {
            $data = resolve(Instagram28Scraper::class)->feedById($val['id'], $val['posts'] ?? 12);
        } else {
            $data = resolve(Instagram28Scraper::class)->feed($val['login'], $val['posts'] ?? 12);
        }

        return new ApiSuccess('Instagram feed', [
            'sdata' => $data,
            'count' => count($data),
        ]);
    }

    public function rapidBoboIgFeed(Request $request)
    {
        $val = $request->validate([
            'login' => 'required',
            'posts' => 'integer|min:1'
        ]);
        $data = resolve(InstagramBoboScraper::class)->feed($val['login'], $val['posts'] ?? 12);
        return new ApiSuccess('Instagram feed', [
            'sdata' => $data,
            'count' => count($data),
        ]);
    }

    public function rapidBoboIgProfile(Request $request, LoginStats $stats)
    {
        $val = $request->validate([ 'login' => 'required' ]);
        $scraper = InstagramBoboScraper::class;
        $stats->put($request, $scraper);
        $sdata = resolve($scraper)->profile($val['login']);
        $stats->put($request, $scraper, true);
        return new ApiSuccess('Instagram profile', [
            'sdata' => $sdata,
        ]);
    }

    // https://rapidapi.com/arraybobo/api/instagram-scraper-2022
    #[Group('scraper')]
    #[Endpoint('scrape/instagram/media/bobo')]
    #[Verbs(D::GET)]
    #[Text('Данные медиа(фото/видео/p/tv/reels) Instagram')]
    #[Param('code', true, D::STRING, 'хеш-код публикации', 'CXJR5eOMXFV')]
    public function rapidBoboIgMedia(Request $request)
    {
        $val = $request->validate([ 'code' => 'required' ]);

        return new ApiSuccess('Instagram media', [
            'sdata' => resolve(InstagramBoboScraper::class)->media($val['code']),
        ]);
    }

    public function rapid12IgProfile(Request $request, LoginStats $stats)
    {
        $val = $request->validate([ 'login' => 'required' ]);
        $scraper = Instagram12Scraper::class;
        $stats->put($request, $scraper);
        $sdata = resolve($scraper)->profile($val['login']);
        $stats->put($request, $scraper, true);

        return new ApiSuccess('Instagram profile', [
            'sdata' => $sdata,
        ]);
    }

    public function rapid12IgFeed(Request $request)
    {
        $val = $request->validate([
            'login' => 'required',
            'posts' => 'integer|min:1'
        ]);
        $data = resolve(Instagram12Scraper::class)->feed($val['login'], $val['posts'] ?? 12);
        return new ApiSuccess('Instagram feed', [
            'sdata' => $data,
            'count' => count($data),
        ]);
    }

    // https://rapidapi.com/herosAPI/api/instagram-data12
    #[Group('scraper')]
    #[Endpoint('scrape/instagram/media/data')]
    #[Verbs(D::GET)]
    #[Text('Данные медиа(фото/видео/p/tv/reels) Instagram')]
    #[Param('code', true, D::STRING, 'хеш-код публикации', 'CXJR5eOMXFV')]
    public function rapid12IgMedia(Request $request)
    {
        $val = $request->validate([ 'code' => 'required' ]);

        return new ApiSuccess('Instagram media', [
            'sdata' => resolve(Instagram12Scraper::class)->media($val['code']),
        ]);
    }

    /**
     * @throws RequestException
     */
    public function getProxyRequest($url) {
        $proxyProviderUrl = config('scrapers.url_proxy_provider');
        if($proxyProviderUrl==false) {
            $response = Http::withOptions([
                'force_ip_resolve' => 'v6',
            ])->get($url);
        }else{
            $response = Http::get($proxyProviderUrl.$url);
        }

        if (!$response->successful()) {
            throw new RequestException($response);
        }

        return $response;
    }

    #[Group('scraper')]
    #[Endpoint('scrape/proxy')]
    #[Verbs(D::GET)]
    #[Text('Прокси на любой url')]
    #[Param('url', true, D::STRING, 'url')]
    public function proxy(Request $request) {
		$url  = $request->url;
		
		$response = $this->getProxyRequest($url);

        return response($response->getBody())->withHeaders(['Content-Type' => $response->headers()['Content-Type']]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/youtube/video/micro')]
    #[Verbs(D::GET)]
    #[Text('Получение данных от скрейпера по YouTube')]
    #[Param('link', true, D::URL)]
    public function microYtVideo(Request $request, YoutubeScraper $scraper)
    {
        $request->validate([
            'link' => 'required',
        ]);

        $video = YoutubeVideo::fromUrl($request->link);

        return [
            'video' => $video,
            'scraper' => get_class($scraper),
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/profile/micro')]
    #[Verbs(D::GET)]
    #[Text('Получение данных от скрейпера по TikTok')]
    #[Param('login', true, D::STRING, 'может быть как логин так и ссылку на сраницу')]
    public function microTtProfile(Request $request, TiktokScraper $scraper)
    {
        $request->validate([
            'login' => 'required',
        ]);

        $user = TTUser::fromLogin($request->login);

        return [
            'user' => $user,
            'scraper' => get_class($scraper),
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/profile/kirtan')]
    #[Verbs(D::GET)]
    #[Text('Данные видео Tiktok')]
    #[Param('link', true, D::URL)]
    public function kirtanTtProfile(Request $request)
    {
        $scraper = resolve(KirtanTiktokScraper::class);
        $data = $scraper->profile($request->user);
        return [
            'sdata' => $data,
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/video/kirtan')]
    #[Verbs(D::GET)]
    #[Text('Данные видео Tiktok')]
    #[Param('url', true, D::URL)]
    public function kirtanTtVideo(Request $request)
    {
        $request->validate([ 'url' => 'required' ]);

        $scraper = resolve(KirtanTiktokScraper::class);
        $data = $scraper->video($request->url);
        return [
            'sdata' => $data,
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/feed/kirtan')]
    #[Verbs(D::GET)]
    #[Text('Данные профиля Tiktok')]
    #[Param('user', true, D::STRING)]
    public function kirtanTtFeed(Request $request)
    {
        $scraper = resolve(KirtanTiktokScraper::class);
        $data = $scraper->feed($request->user);
        return [
            'sdata' => $data,
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/profile/bestexperience')]
    #[Verbs(D::GET)]
    #[Text('Данные профиля Tiktok')]
    #[Param('login', true, D::STRING, 'логин юзера без @', 'alenchik_202009')]
    public function bestExperienceTtProfile(Request $request)
    {
        $request->validate([ 'login' => 'required' ]);

        $scraper = resolve(BestExperienceTiktokScraper::class);
        $data = $scraper->profile($request->login);
        return new ApiSuccess('Tiktok profile', [
            'sdata' => $data,
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/video/bestexperience')]
    #[Verbs(D::GET)]
    #[Text('Данные видео Tiktok')]
    #[Param('url', true, D::URL, 'длинная или короткая ссылка на видео', "https://www.tiktok.com/@alenchik_202009/video/7071896461771934977 & https://vt.tiktok.com/ZSdym3Lbb/")]
    public function bestExperienceTtVideo(Request $request)
    {
        $request->validate([ 'url' => 'required' ]);

        $scraper = resolve(BestExperienceTiktokScraper::class);
        $data = $scraper->video($request->url);
        return new ApiSuccess('Tiktok feed', [
            'sdata' => $data,
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/feed/bestexperience')]
    #[Verbs(D::GET)]
    #[Text('Данные массива видео профиля TikTok')]
    #[Param('login', true, D::STRING, 'логин пользователя без @', 'alenchik_202009')]
    #[Param('posts', false, D::INT, 'кол-во постов, если не заполнено то = 12')]
    public function bestExperienceTtFeed(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'posts' => 'integer|min:1|max:100'
        ]);

        $scraper = resolve(BestExperienceTiktokScraper::class);
        $data = $scraper->feed($request->login, $request->posts ?? 12);
        return new ApiSuccess('Tiktok feed', [
            'sdata' => $data,
        ]);
    }

    // https://rapidapi.com/JoTucker/api/tiktok-scraper2
    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/profile/jo')]
    #[Verbs(D::GET)]
    #[Text('Данные профиля Tiktok')]
    #[Param('login', true, D::STRING, 'логин юзера без @', 'alenchik_202009')]
    public function joTtProfile(Request $request)
    {
        $request->validate([ 'login' => 'required' ]);

        $scraper = resolve(JoTiktokScraper::class);
        $data = $scraper->profile($request->login);
        return new ApiSuccess('Tiktok profile', [
            'sdata' => $data,
        ]);
    }

    // https://rapidapi.com/JoTucker/api/tiktok-scraper2
    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/feed/jo')]
    #[Verbs(D::GET)]
    #[Text('Feed профиля Tiktok')]
    #[Param('login', true, D::STRING, 'логин юзера без @', 'alenchik_202009')]
    #[Param('posts', false, D::INT, 'кол-во постов, если не заполнено то = 12')]
    public function joTtFeed(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'posts' => 'integer|min:1|max:100'
        ]);

        $scraper = resolve(JoTiktokScraper::class);
        $data = $scraper->feed($request->login, $request->posts ?? 12);
        return new ApiSuccess('Tiktok feed', [
            'sdata' => $data,
        ]);
    }

    #[Group('scraper')]
    #[Endpoint('scrape/tiktok/video/jo')]
    #[Verbs(D::GET)]
    #[Text('Данные видео Tiktok')]
    #[Param('url', true, D::URL, 'длинная ссылка на видео', "https://www.tiktok.com/@alenchik_202009/video/7071896461771934977")]
    public function joTtVideo(Request $request)
    {
        $request->validate([ 'url' => 'required' ]);

        $scraper = resolve(JoTiktokScraper::class);
        $data = $scraper->video($request->url);
        return [
            'sdata' => $data,
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/vk/profile/api')]
    #[Verbs(D::GET)]
    #[Text('Пользователь VK')]
    #[Param('login', true, D::STRING, 'логин пользователя', 'durov')]
    public function apiVkProfile(Request $request)
    {
        $request->validate([ 'login' => 'required' ]);

        $scraper = resolve(VkApiScraper::class);
        $data = $scraper->profileUser($request->login);
        return [
            'sdata' => $data,
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/vk/club/api')]
    #[Verbs(D::GET)]
    #[Text('Группа VK')]
    #[Param('login', true, D::STRING)]
    public function apiVkClub(Request $request)
    {
        $request->validate([ 'login' => 'required' ]);

        $scraper = resolve(VkApiScraper::class);
        $data = $scraper->profileClub($request->login);
        return [
            'sdata' => $data,
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/vk/media/api')]
    #[Verbs(D::GET)]
    #[Text('Медиа VK')]
    #[Param('link', true, D::STRING, 'ссылка на фото/видео/запись на стене')]
    public function apiVkMedia(Request $request)
    {
        $request->validate([ 'link' => 'required' ]);

        $scraper = resolve(VkApiScraper::class);
        $data = $scraper->media($request->link);
        return [
            'sdata' => $data,
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/telegram/followers')]
    #[Verbs(D::GET)]
    #[Text('Подписчики Telegram')]
    #[Param('login', true, D::STRING, 'имя канала/группы')]
    public function telegramFollowers(Request $request)
    {
        $request->validate([ 'login' => 'required|string' ]);

        $scraper = resolve(TelegramScraper::class);
        $data = $scraper->profile($request->login);
        return [
            'sdata' => $data,
        ];
    }

    #[Group('scraper')]
    #[Endpoint('scrape/telegram/views')]
    #[Verbs(D::GET)]
    #[Text('Просмотры постов Telegram')]
    #[Param('login', true, D::STRING, 'имя канала/группы')]
    #[Param('id', true, D::INT, 'id поста')]
    public function telegramViews(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'id'    => 'required|integer'
        ]);

        $scraper = resolve(TelegramScraper::class);
        $data = $scraper->views($request->login, $request->id);
        return [
            'sdata' => $data,
        ];
    }
}
