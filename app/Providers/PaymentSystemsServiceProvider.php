<?php

namespace App\Providers;

use App;
use App\PaymentSystems\BankTransferPaymentSystem;
use App\PaymentSystems\BePaidPaymentSystem;
use App\PaymentSystems\CardsInternationalPaymentSystem;
use App\PaymentSystems\ConnectumPaymentSystem;
use App\PaymentSystems\EWalletPaymentSystem;
use App\PaymentSystems\MonerchyPaymentSystem;
use App\PaymentSystems\PaymentSystemProxy;
use App\PaymentSystems\PayOpPaymentSystem;
use App\PaymentSystems\PayOpTestPaymentSystem;
use App\PaymentSystems\PayPalPaymentSystem;
use App\PaymentSystems\PayPalRubToEurPaymentSystem;
use App\PaymentSystems\PayPalRubToUsdPaymentSystem;
use App\PaymentSystems\PayToDayPaymentSystem;
use App\PaymentSystems\PayzePaymentSystem;
use App\PaymentSystems\PoliPaymentSystem;
use App\PaymentSystems\RevolutPaymentSystem;
use App\PaymentSystems\StripeEdTechRemotePaymentSystem;
use App\PaymentSystems\StripePaymentSystem;
use App\PaymentSystems\StripeRemotePaymentSystem;
use App\PaymentSystems\StripeSeoAdvRemotePaymentSystem;
use App\Services\Admin\PaymentMethodIconsService;
use App\Services\Admin\PaymentSystemsService;
use App\Services\PaymentMethodsService;
use Illuminate\Support\ServiceProvider;

class PaymentSystemsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind(StripePaymentSystem::class, function () {
            $config = 'payment-systems.stripe';

            return new StripePaymentSystem(
                config("$config.secret"),
                config("$config.hook_secret"),
                new PaymentSystemProxy(
                    config("$config.proxy.url"),
                    config("$config.proxy.cipher"),
                    config("$config.proxy.key"),
                    config("$config.proxy.iv"),
                )
            );
        });

        //Deprecated
        App::bind(StripeRemotePaymentSystem::class, function () {
            $config = 'payment-systems.stripe_remote.seoAdv';

            return new StripeRemotePaymentSystem(
                config("$config.url"),
                config("$config.meta_key"),
                config("payment-systems.stripe_remote.test"),
            );
        });

        App::bind(StripeSeoAdvRemotePaymentSystem::class, function () {
            $config = 'payment-systems.stripe_remote.seoAdv';

            return new StripeSeoAdvRemotePaymentSystem(
                config("$config.url"),
                config("$config.meta_key"),
                config("payment-systems.stripe_remote.test"),
            );
        });

        App::bind(StripeEdTechRemotePaymentSystem::class, function () {
            $config = 'payment-systems.stripe_remote.edTech';

            return new StripeEdTechRemotePaymentSystem(
                config("$config.url"),
                config("$config.meta_key"),
                config("payment-systems.stripe_remote.test"),
            );
        });

        App::bind(ConnectumPaymentSystem::class, function () {
            $config = 'payment-systems.connectum';

            return new ConnectumPaymentSystem(
                config("$config.sandbox"),
                config("$config.user"),
                config("$config.password"),
                storage_path(config("$config.key_file")),
                config("$config.key_password"),
            );
        });

        App::bind(PayPalPaymentSystem::class, function () {
            $config = 'payment-systems.paypal';

            return new PayPalPaymentSystem(
                config("$config.sandbox"),
                config("$config.client_id"),
                config("$config.secret"),
                new PaymentSystemProxy(
                    config("$config.proxy.url"),
                    config("$config.proxy.cipher"),
                    config("$config.proxy.key"),
                    config("$config.proxy.iv"),
                )
            );
        });

        App::bind(PayPalRubToEurPaymentSystem::class, function () {
            $config = 'payment-systems.paypal';

            return new PayPalRubToEurPaymentSystem(
                config("$config.sandbox"),
                config("$config.client_id"),
                config("$config.secret"),
                new PaymentSystemProxy(
                    config("$config.proxy.url"),
                    config("$config.proxy.cipher"),
                    config("$config.proxy.key"),
                    config("$config.proxy.iv"),
                )
            );
        });

        App::bind(PayPalRubToUsdPaymentSystem::class, function () {
            $config = 'payment-systems.paypal';

            return new PayPalRubToUsdPaymentSystem(
                config("$config.sandbox"),
                config("$config.client_id"),
                config("$config.secret"),
                new PaymentSystemProxy(
                    config("$config.proxy.url"),
                    config("$config.proxy.cipher"),
                    config("$config.proxy.key"),
                    config("$config.proxy.iv"),
                )
            );
        });

        App::bind(PayOpPaymentSystem::class, function () {
            $config = 'payment-systems.payOp';

            return new PayOpPaymentSystem(
                config("$config.publicKey"),
                config("$config.secretKey")
            );
        });

        App::bind(PoliPaymentSystem::class, function () {
            $config = 'payment-systems.payOp';

            return new PoliPaymentSystem(
                config("$config.publicKey"),
                config("$config.secretKey")
            );
        });

        App::bind(RevolutPaymentSystem::class, function () {
            $config = 'payment-systems.payOp';

            return new RevolutPaymentSystem(
                config("$config.publicKey"),
                config("$config.secretKey")
            );
        });

        App::bind(CardsInternationalPaymentSystem::class, function () {
            $config = 'payment-systems.payOp';

            return new CardsInternationalPaymentSystem(
                config("$config.publicKey"),
                config("$config.secretKey")
            );
        });

        App::bind(BankTransferPaymentSystem::class, function () {
            $config = 'payment-systems.payOp';

            return new BankTransferPaymentSystem(
                config("$config.publicKey"),
                config("$config.secretKey")
            );
        });

        App::bind(PayOpTestPaymentSystem::class, function () {
            $config = 'payment-systems.payOp';

            return new PayOpTestPaymentSystem(
                config("$config.publicKey"),
                config("$config.secretKey")
            );
        });

        App::bind(EWalletPaymentSystem::class, function () {
            $config = 'payment-systems.payOp';

            return new EWalletPaymentSystem(
                config("$config.publicKey"),
                config("$config.secretKey")
            );
        });

        App::bind(PayzePaymentSystem::class, function () {
            $config = 'payment-systems.payze';

            return new PayzePaymentSystem(
                config("$config.apiKey"),
                config("$config.apiSecret")
            );
        });

        App::bind(MonerchyPaymentSystem::class, function () {
            $config = 'payment-systems.monerchy';

            return new MonerchyPaymentSystem(
                config("$config.merchantID"),
                config("$config.apiKeyToken"),
                config("$config.isDebugMode"),
            );
        });

        App::bind(BePaidPaymentSystem::class, function () {
            $config = 'payment-systems.bepaid';

            return new BePaidPaymentSystem(
                config("$config.shopId"),
                config("$config.shopSecret"),
                config("$config.isTestMode"),
                config("$config.isDebugMode")
            );
        });

        App::singleton(PaymentSystemsService::class, function () {
            $config = 'payment-systems.admin';

            return new PaymentSystemsService(
                config("$config.paymentSystems"),
                config("$config.iconsBaseDir"),
                config("countries")
            );
        });

        App::singleton(PaymentMethodsService::class, function () {
            $config = 'payment-systems.admin';

            return new PaymentMethodsService(
                config("$config.paymentSystems")
            );
        });

        App::singleton(PaymentMethodIconsService::class, function () {
            $config = 'payment-systems.admin';

            return new PaymentMethodIconsService(
                config("$config.iconsBaseDir")
            );
        });

        App::singleton(PayToDayPaymentSystem::class, function () {
            $config = 'payment-systems.payToDay';

            return new PayToDayPaymentSystem(
                config("$config.shop_id"),
                config("$config.api_key"),
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
    }
}
