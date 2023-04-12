<?php

use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Transformers\Tiktok\SetImgLogin;
use App\Exceptions\NonReportable\BadParameterException;
use App\Http\Controllers\Admin\PaymentSystemsController;
use App\Http\Controllers\CashbackController;
use App\Http\Controllers\GoogleAnalyticsController;
use App\Http\Controllers\KeitaroPostbackProxy;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentSystems\BePaidController;
use App\Http\Controllers\PaymentSystems\CentController;
use App\Http\Controllers\PaymentSystems\ConnectumController;
use App\Http\Controllers\PaymentSystems\CryptoCloudController;
use App\Http\Controllers\PaymentSystems\FondyController;
use App\Http\Controllers\PaymentSystems\MonerchyController;
use App\Http\Controllers\PaymentSystems\PaymoreController;
use App\Http\Controllers\PaymentSystems\PayOpController;
use App\Http\Controllers\PaymentSystems\PayPalController;
use App\Http\Controllers\PaymentSystems\PayzeController;
use App\Http\Controllers\PaymentSystems\StripeController;
use App\Http\Controllers\PaymentSystems\StripeRemoteController;
use App\Http\Controllers\PaymentSystems\YooController;
use App\Http\Controllers\PremiumStatusesController;
use App\Http\Controllers\PricesController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\SendpulseController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\UserServicesController;
use App\Responses\ApiError;
use App\Routes as R;
use App\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// --------------- for testing ---------------------
Route::get('ping', 'PingController@pong');
Route::get('long', 'PingController@long');
Route::get('slow', 'PingController@slow');

R::Auth(fn () => Route::any('test_auth', 'PingController@testAuth'));
R::Seo(fn () => Route::get('seo/ping', fn () => 'the seo ping'));
R::Proxy(fn () => Route::get('proxies/ping', fn () => 'the proxy ping'));
R::Moderator(fn () => Route::any('test_moderator', 'PingController@testModerator'));
R::Admin(fn () => Route::any('test_admin', 'PingController@testAdmin'));

// --------------------------------------------------
//               public routes
// --------------------------------------------------

//      ---------- auth --------------
Route::post('login/local', 'AuthController@localLogin')->name('local_login');

Route::prefix('login')->group(function () {
    Route::prefix('facebook')->group(function () {
        Route::post('', 'AuthController@facebookLogin');
        Route::post('confirm', 'AuthController@facebookLoginWithConfirm');
        Route::post('select', 'AuthController@facebookSelectLogin');
    });

    Route::prefix('vk')->group(function () {
        Route::post('', 'AuthController@vkLogin');
        Route::post('code', 'AuthController@vkLoginWithCode');
        Route::get('callback', 'AuthController@vkLoginCallback');
        Route::post('confirm', 'AuthController@vkLoginWithConfirm');
        Route::post('select', 'AuthController@vkSelectLogin');

        // Убрать после внедрения socialite
        Route::get('get_sdk', function () {
            $url = 'https://vk.com/js/api/openapi.js?169';
            $res = Http::get($url);
            return response($res->body())->withHeaders(['Content-Type' => 'text/javascript']);
        });
    });
});

Route::post('register', 'AuthController@register')->name('register');

Route::get('confirm/{confirmationCode}', 'AuthController@confirm')
    ->name('confirmation_path');

Route::post('reset', 'AuthController@reset'); // generate reset code
Route::post('set_password', 'AuthController@setPassword'); // use reset code

// внешние сервисы
Route::get('extern_services', 'ServicesController@index');
Route::get('region', 'RegionsController@region');


Route::get('user_services/find', [UserServicesController::class, 'find']);
Route::get('user_services/tiny', [UserServicesController::class, 'tiny']);
Route::get('user_services/tags', [UserServicesController::class, 'tags']);

Route::get('user_services/{tag}/prices', 'USPricesController@byTag');

Route::post('prices/costs', [PricesController::class, 'costs']);
Route::get('prices/tiny', [PricesController::class, 'tiny']);
Route::get('prices/orderCost', [PricesController::class, 'orderCost']);

// ------- Программа лояльности ------------
Route::get('premium_statuses', 'PremiumStatusesController@index');

// Получение списка статей
Route::get('articles', 'ArticleController@index');
// Получение уникальной статьи
Route::get('articles/{slug}', 'ArticleController@show');

// создание заказов на главной
Route::post('c_orders/main', 'CompositeOrdersController@main')
    ->name('create_order_main');
// создание группы заказов на главной
Route::post('c_orders/pack', 'CompositeOrdersController@pack')
    ->name('create_orders_pack');
// повторение платежа
Route::post('c_orders/repay', 'CompositeOrdersController@repay')
    ->name('create_orders_repay');

// создание заказов из приложения
Route::post('c_orders/app', 'CompositeOrdersController@mainApp')
    ->name('create_order_mainApp');
// =============== payment systems ============================
// webhooks
Route::post('cent_status', [CentController::class, 'hook']);
Route::post('connectum_status', [ConnectumController::class, 'hook']);
Route::post('fondy_status', [FondyController::class, 'hook']);
Route::post('paymore_status', [PaymoreController::class, 'hook']);
Route::post('stripe_status', [StripeController::class, 'hook']);
Route::post('yk_status', [YooController::class, 'hook']);
Route::post('payop_status', [PayOpController::class, 'hook']);
Route::post('cryptocloud_status', [CryptoCloudController::class, 'hook']);
Route::post('paypal_status', [PayPalController::class, 'hook']);
Route::post('payze_status', [PayzeController::class, 'hook'])->name('payze_hook');
Route::post('monerchy_status', [MonerchyController::class, 'hook'])->name('monerchy_hook');
Route::any('bepaid_status', [BePaidController::class, 'hook'])->name('bepaid_hook');
// перенаправение после платежа
Route::post('fondy_redirect', [FondyController::class, 'redirect']);
Route::get('connectum_redirect', [ConnectumController::class, 'redirect'])->name('connectum_redirect');

// данные о платеже
Route::get('payment/systems', [PaymentController::class, 'systems']);
Route::get('payment/methods', [PaymentController::class, 'methods']);
Route::get('payment/{id}', [PaymentController::class, 'status']);
// ============================================================

//proxy methods
Route::post('stripe_remote_status', [StripeRemoteController::class, 'hook'])->name('stripe:callback');
Route::post('collect/analytic/{type}', [GoogleAnalyticsController::class, 'collect']);
Route::post('keitaro-postback', [KeitaroPostbackProxy::class, 'post']);

// misc
Route::get('c_orders/uuid/{uuid}', 'CompositeOrdersController@showByUUID');

Route::get('cur/rate-to/{currency1}/{currency2}', 'CurrenciesController@rateTo')
    ->where(['currency1' => '[A-Z]{3,4}', 'currency2' => '[A-Z]{3,4}']);

Route::post('sp/cart', [SendpulseController::class, 'createAbandonedСart']);
Route::post('sp/balance', [SendpulseController::class, 'createNotTopUpBalance']);
Route::post('sp/order', [SendpulseController::class, 'createUnpaidOrder']);

// ------------- Требуется авторизация -----------
R::Auth(function () {
    // --------------- user ---------------------------
    Route::get('user', 'UsersController@details');
    Route::get('user2', 'UsersController@details2');
    Route::post('logout', 'AuthController@logout');

    Route::post('user', 'UsersController@update');

    Route::get('user/refs', [UsersController::class, 'getRefs']);
    Route::get('user/refs/total', [UsersController::class, 'getRefsCount']);

    // transactions
    Route::get('user/transactions', [TransactionsController::class, 'transactions'])
        ->name('user.transactions.search');

    Route::get('user/transactions/totals', [TransactionsController::class, 'totals'])
        ->name('user.transactions.totals');

    // Cashback
    Route::get('user/cashback', [CashbackController::class, 'transactions'])
        ->name('user.cashback.search');

    Route::get('user/cashback/totals', [CashbackController::class, 'totals'])
        ->name('user.cashback.totals');

    Route::get('user/premium_statuses', [PremiumStatusesController::class, 'forUser']);

    Route::post('update_password', 'AuthController@updatePassword'); // return token
    Route::post('update_avatar', 'AuthController@updateAvatar');

    // Загрузка изображения
    Route::post('img/upload', 'ImageUploadController@imageUploadPost');

    // notifications
    Route::apiResource('notifications', 'NotificationsController', [
        'except' => ['show'],
    ]);

    // prices
    Route::apiResource('prices', 'PricesController', [
        'except' => ['show'],
    ]);

    // список заказов
    Route::get('c_orders', 'SearchController@ySearchForUser')
        ->name('user.search');

    // создание заказов
    Route::post('c_orders', 'CompositeOrdersController@create')
        ->name('create_order');

    // асинхронное создание заказов
    Route::post('c_orders/async', 'CompositeOrdersController@createAsync')
        ->name('create_order_async');

    /**
     * Payment systems
     */
    Route::prefix('deposit')->name('deposit.')->group(function () {
        Route::post('', [PaymentController::class, 'deposit']);
        Route::post('yk', [YooController::class, 'deposit']);
        Route::post('fondy', [FondyController::class, 'deposit']);
        Route::post('cent', [CentController::class, 'deposit']);
        Route::post('paymore', [PaymoreController::class, 'deposit']);
    });

    //информация по заказу
    Route::get('c_orders/{id}', 'CompositeOrdersController@show');

    // вывод средств
    //    Route::post('withdrawal', [TransactionsController::class, 'withdraw']);

    //    Route::get('services/{service_id}/cost_premium/{n}', 'ServicesController@costPremium');
    //    Route::post('services/costs_premium', 'ServicesController@costsPremium');
});

// информация по заказу
// нужно для тестирования
Route::post('c_orders/{id}/update', 'CompositeOrdersController@updateCommand');

R::Seo(function () {
    // Получение статей пользователя
    Route::get('articles_by_user', 'ArticleController@articlesByUser');
    // Создание статьи
    Route::post('articles', 'ArticleController@store');
    // Изменение статьи
    Route::post('articles/{id}', 'ArticleController@update');
    // Удаление статьи
    Route::delete('articles/{id}', 'ArticleController@destroy');
});

R::Proxy(function () {
    Route::get('proxies', 'ProxiesController@list');
    Route::post('proxies', 'ProxiesController@store');
    Route::post('proxies/{id}', 'ProxiesController@update');
    Route::delete('proxies/{id}', 'ProxiesController@destroy');
});

// ------------- Требуются права менеджера -----------
R::Manager(function () {
    // Получение списка заказов, поиск заказов модератором
    Route::get('ysearch', 'SearchController@ySearch')->name('admin.search');

    // for tests
    Route::get('admin/users', 'Admin\UsersController@index');
    //Обновление пароля пользователя
    Route::post('admin/users/password', 'UsersController@updatePassword');

    Route::get('admin/find', 'Admin\UsersController@find');
    Route::get('admin/user/{user_id}/refs', 'Admin\ReferalController@index');
    Route::get('admin/user/{user_id}/transactions', 'Admin\UsersController@transactions');
    Route::get('admin/user/{user_id}/payments', 'Admin\UsersController@payments');

    Route::prefix('admin/payment-systems')->group(function () {
        Route::get('data', [PaymentSystemsController::class, 'data']);
        Route::post('orders', [PaymentSystemsController::class, 'changePaymentMethodOrders']);

        Route::prefix('method')->group(function () {
            Route::delete('/{methodId}', [PaymentSystemsController::class, 'deletePaymentMethod'])
                ->whereNumber('methodId');
            Route::post('/', [PaymentSystemsController::class, 'createPaymentMethod']);
            Route::put('/', [PaymentSystemsController::class, 'updatePaymentMethod']);
            Route::post('/uploadTmpIcon', [PaymentSystemsController::class, 'uploadTmpPaymentMethodIcon']);
        });
    });
});

R::Moderator(function () {
    Route::get('statistics', 'StatisticsController@all');

    Route::get('user_services/{tag}/labels/list', 'UserServicesController@listLabels');
    Route::post('user_services/{tag}/labels/add', 'UserServicesController@addLabels');
    Route::post('user_services/{tag}/labels/remove', 'UserServicesController@removeLabels');

    // Отключение пользовательского сервиса
    Route::post('user_services/disconnect', 'UserServicesController@disconnectById');
    // Включение пользовательского сервиса
    Route::post('user_services/connect', 'UserServicesController@connectById');
    // Обновление пользовательского сервиса
    Route::post('user_services/{tag}', 'UserServicesController@update');
    // Обновление цен пользовательского сервиса
    Route::post('user_services/{tag}/prices', 'USPricesController@update');

    Route::get('moderator/getPrecent', 'UserServicesController@getGroupForModerate');
    Route::post('moderator/updatePrecent', 'UserServicesController@updatePercentAndCountGroup');

    // CompositeOrders
    //    Route::post('moderator/changeOrderStatus', 'CompositeOrdersController@changeOrderStatus');
    Route::post('c_orders/{action}', 'CompositeOrdersController@changeOrderStatus');

    Route::get('moderator/order/{id}', 'CompositeOrdersController@show');
    Route::get('c_orders/{id}/logs', 'CompositeOrdersController@OLogs');
    //    Route::post('c_orders/{id}/restart', 'CompositeOrdersController@restart');

    // Изменение баланса пользователя
    Route::post('admin/users/balance', 'UsersController@updateBalance');
    // Изменение статуса пользователя
    Route::post('admin/users/status', 'PremiumStatusesController@update');

    // Выводит exceptions, тестовый роут
    Route::get('test/exception', [\App\Http\Controllers\PingController::class, 'testException']);
});

// параметры конфигурации для классов внешних сервисов
R::Admin(function () {
    // Роли пользователя
    Route::get('user/roles/all', 'UsersController@allRoles');
    Route::get('user/{id}/roles', 'UsersController@getRoles');
    Route::post('user/{id}/roles', 'UsersController@addRole');
    Route::delete('user/{id}/roles', 'UsersController@removeRole');
});

// ------- заработок ------------
R::Auth(function () {
    // Доступные задания
    Route::get('userjobs', 'UserJobsController@index')->name('list_jobs');
    // Взять задание
    Route::post('userjobs/{id}', 'UserJobsController@create')->name('take_job');
});

// Проверить задание
Route::post('userjobs/check/{actionId}', 'UserJobsController@check')->name('check_job');

// ---------------------- Для тестов ----------------------

//Route::get('cat', function (RequestWithTag $request) {
//    $request = App::make(FirstRequest::class);
//
//    return [/admin/find
//        'response' => $request->all(),
//    ];
//});


Route::get('cat2', function () {
    $svc = App::make(ANakrutka::class);
    //    $res = $svc->nakrutka->status(['orders' => [1325287]]);

    $res = Http::asForm()->post(env('NAKRUTKA_API_URL'), [
        'key' => env('NAKRUTKA_API_KEY'),
        'action' => 'status',
        'orders' => '1325287,1327118,1331023,1330886',
        //        'order' => 1325287,
    ])->json();

    return [
        'response' => $res,
    ];
});

// -------------------- телеграм бот ------------------

Route::post(
    'telegram/check/{telegramId}',
    'TelegramController@getUserByTelegramId'
);

R::Auth(function () {

    //    https://t.me/SmmTouchBot?start=NHfmwji3Jxk5YszI0i4u6Aj5hxUTW5UUE9jOky67KWfd3wUvBK9X0Xmee4qi
    Route::post(
        'telegram/connect/{telegramId}',
        'TelegramController@connectAccounts'
    );

    Route::post('telegram/deposit', 'TelegramController@deposit');
});


// ----------------------------------------------------
Route::get('scrape/instagram/list_for_app', [ScraperController::class, 'listForApp']);

Route::get('scrape/instagram/profile/micro', [ScraperController::class, 'microIgProfile']);
Route::get('scrape/instagram/media/micro', [ScraperController::class, 'microIgMedia']);
Route::get('scrape/instagram/feed/micro', [ScraperController::class, 'microIgFeed']);

Route::get('scrape/instagram/profile/rapid85', [ScraperController::class, 'rapid85IgProfile']);
Route::get('scrape/instagram/media/rapid85', [ScraperController::class, 'rapid85IgMedia']);
Route::get('scrape/instagram/feed/rapid85', [ScraperController::class, 'rapid85IgFeed']);

Route::get('scrape/instagram/profile/rapid39', [ScraperController::class, 'rapid39IgProfile']);
Route::get('scrape/instagram/media/rapid39', [ScraperController::class, 'rapid39IgMedia']);
Route::get('scrape/instagram/feed/rapid39', [ScraperController::class, 'rapid39IgFeed']);

Route::get('scrape/instagram/profile/rapid28', [ScraperController::class, 'rapid28IgProfile']);
Route::get('scrape/instagram/media/rapid28', [ScraperController::class, 'rapid28IgMedia']);
Route::get('scrape/instagram/feed/rapid28', [ScraperController::class, 'rapid28IgFeed']);

Route::get('scrape/instagram/profile/bobo', [ScraperController::class, 'rapidBoboIgProfile']);
Route::get('scrape/instagram/media/bobo', [ScraperController::class, 'rapidBoboIgMedia']);
Route::get('scrape/instagram/feed/bobo', [ScraperController::class, 'rapidBoboIgFeed']);

Route::get('scrape/instagram/profile/rapid12', [ScraperController::class, 'rapid12IgProfile']);
Route::get('scrape/instagram/media/rapid12', [ScraperController::class, 'rapid12IgMedia']);
Route::get('scrape/instagram/feed/rapid12', [ScraperController::class, 'rapid12IgFeed']);

Route::get('scrape/proxy', [ScraperController::class, 'proxy']);

Route::get('scrape/youtube/video/micro', [ScraperController::class, 'microYtVideo']);


Route::get('scrape/tiktok/profile/micro', [ScraperController::class, 'microTtProfile']);

Route::get('scrape/tiktok/profile/kirtan', [ScraperController::class, 'kirtanTtProfile']);
Route::get('scrape/tiktok/video/kirtan', [ScraperController::class, 'kirtanTtVideo']);
Route::get('scrape/tiktok/feed/kirtan', [ScraperController::class, 'kirtanTtFeed']);

Route::get('scrape/tiktok/profile/bestexperience', [ScraperController::class, 'bestExperienceTtProfile']);
Route::get('scrape/tiktok/feed/bestexperience', [ScraperController::class, 'bestExperienceTtFeed']);

Route::get('scrape/tiktok/profile/jo', [ScraperController::class, 'joTtProfile']);
Route::get('scrape/tiktok/feed/jo', [ScraperController::class, 'joTtFeed']);


Route::get('scrape/vk/profile/api', [ScraperController::class, 'apiVkProfile']);
Route::get('scrape/vk/club/api', [ScraperController::class, 'apiVkClub']);
Route::get('scrape/vk/media/api', [ScraperController::class, 'apiVkMedia']);

Route::get('scrape/telegram/followers', [ScraperController::class, 'telegramFollowers']);
Route::get('scrape/telegram/views', [ScraperController::class, 'telegramViews']);

Route::get('tf', function () {
    $t = new SetImgLogin();
    $res = $t->baseCurl('http://httpbin.org/get');

    return [
        'env' => App::environment(),
        'res' => $res,
    ];
});


Route::get('img/{filename}', function ($filename, \Illuminate\Http\Request $request) {
    //    $url = '/img/6f71bb4168ebcde94be5190346ad262a.jpg';
    $res = Http::get('http://img-proxy:6869' . '/img/' . $filename);
    return response($res->body(), 200, $res->headers());
});

Route::get('test/x', function () {
    //    throw new \App\Exceptions\NonReportable\BadCurrencyException('rub');
    throw new BadParameterException(__('s.inflow_positive'));
});

Route::get('test/error', function () {
    return new ApiError(__('s.login_error'));
});

Route::post('test/log_request', function (Illuminate\Http\Request $req) {
    if (!env('REQUEST_LOGGING_ENDPOINT', false)) {
        return response('', 404);
    }
    \Illuminate\Support\Facades\Log::info(json_encode($req->all()));
    return response('', 200);
});

Route::get('test/validation', function (Illuminate\Http\Request $req) {
    $req->validate([
        'name' => 'required',
        'age' => 'required|integer',
    ]);
});

Route::get('test/notfound', function () {
    $user = User::findOrFail(98_765_432);
    return 'oops';
});

Route::get('documentor', function () {
    $svc = new \App\Documentor\DocumentorService();

    return $svc->getData();
});

Route::get('cat', function () {

    return ['user' => maybe_user()];
});

// Данный маршрут предназначен для того чтобы get запрос от keitaro (s2s postback)
// ппреобразовать в post запрос на appsflyer
// доки по appsflyer: https://support.appsflyer.com/hc/en-us/articles/207034486-Server-to-server-events-API-for-mobile-S2S-mobile-
// Из общения с саппортом keitaro: На данный момент передача из Кеитаро в Appsflyer недоступна в нормальном виде, так как Appsflyer
// может принимать только запросы с телом, но у нас есть возможность отправить только запрос с utm метками из трекера
// поэтому единственный реальный для вас вариант сейчас, это получать в трекер постбеки, а потом слать куда то на свой api,
// где запрос будет пересобран и отправлен в правильном виде для Appsflyer
Route::get('afpostback/{appname}', function ($appname, \Illuminate\Http\Request $req) {
    $input = $req->all();
    $devkey = config('app.AF_DEV_KEY');

    return Http::withHeaders([
        'authentication' => $devkey
    ])->post('https://api2.appsflyer.com/inappevent/' . $appname, $input);
});
