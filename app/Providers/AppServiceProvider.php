<?php

namespace App\Providers;

use App;
use App\Http\Middleware\CheckUserRole;
use App\Role\RoleChecker;
use App\Services\Currency\CbrRateService;
use App\Services\CurrencyService;
use App\Services\DistributionService;
use App\Services\EventsSchemesService;
use App\Services\GoogleAnalytics;
use App\Services\LoginStats;
use App\Services\InstagramNotificationService;
use App\Services\Money\PaymentService;
use App\Services\Money\Services\CashbackService;
use App\Services\Money\Services\TransactionsService;
use App\Services\MoneyService;
use App\Services\ProfilerService;
use App\Services\ServicesService;
use App\Services\SMMAuthService;
use App\Services\VKTransport;
use App\XTimer;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

use App\Services\Money\Services\PaymentService as PaymentServiceBase;
use App\Services\SendpulseService;

class AppServiceProvider extends ServiceProvider
{

//    public $bindings = [
//        CurrencyService::class => CurrencyService::class,
//    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind(SMMAuthService::class, function () {
            return new SMMAuthService();
        });

        app()->singleton(VKTransport::class, function () {
            return new VKTransport();
        });

        $this->app->singleton(CheckUserRole::class,
            function (Application $app) {
                return new CheckUserRole(
                    $app->make(RoleChecker::class)
                );
            });

        App::bind(ServicesService::class, function() {
            return new ServicesService();
        });

        App::bind(CurrencyService::class, function() {
            return new CurrencyService();
        });

        App::bind(GoogleAnalytics::class, function() {
            return new GoogleAnalytics();
        });

        App::bind(XTimer::class, XTimer::class);

        app()->singleton(ProfilerService::class, function() {
            return new ProfilerService();
        });

        App::bind(MoneyService::class, function () {
            return new MoneyService();
        });


        App::bind(InstagramNotificationService::class, function () {
            return new InstagramNotificationService(config('services.instagram_notification'));
        });

        App::bind(LoginStats::class, function () {
            return new LoginStats(config('services.login_stats.spreadsheet_id'));
        });

        App::bind(CashbackService::class, function () {
            return new CashbackService();
        });

        App::bind(TransactionsService::class, function () {
            return new TransactionsService();
        });

        App::bind(PaymentService::class, function () {
            return new PaymentServiceBase(app(TransactionsService::class), app(CashbackService::class));
        });

        App::bind(SendpulseService::class, function () {
            return new SendpulseService(
                config('services.sendpulse.client_id'),
                config('services.sendpulse.secret')
            );
        });

        App::bind(EventsSchemesService::class, function () {
            return new EventsSchemesService(app(SendpulseService::class));
        });

        App::singleton(DistributionService::class, function () {
            return new DistributionService();
        });

        App::singleton(CurrencyService::class, function () {
            return new CurrencyService(
                new CbrRateService()
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Http::macro('useProxy', function () {
            $options = [
                'proxy' => config('app.FBA_PROXY'),
            ];
            return Http::withOptions($options);
        });
    }
}
