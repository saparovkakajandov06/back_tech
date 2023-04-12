<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Everve\EverveFake;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\Nakrutka\NakrutkaFake;
use App\Domain\Services\NakrutkaAuto\ANakrutkaAuto;
use App\Domain\Services\NakrutkaAuto\NakrutkaAutoFake;
use App\Domain\Services\Prm4u\APrm4u;
use App\Domain\Services\Prm4u\Prm4uFake;
use App\Domain\Services\Prm4uAuto\APrm4uAuto;
use App\Domain\Services\Prm4uAuto\Prm4uAutoFake;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Socgress\SocgressFake;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\Domain\Services\Vkserfing\VkserfingFake;
use App\Domain\Services\VkserfingAuto\AVkserfingAuto;
use App\Domain\Services\VkserfingAuto\VkserfingAutoFake;
use App\Domain\Services\Vtope\AVtope;
use App\Domain\Services\Vtope\VtopeFake;
use App\PaymentSystems\BankTransferPaymentSystem;
use App\PaymentSystems\CardsInternationalPaymentSystem;
use App\PaymentSystems\CentPaymentSystem;
use App\PaymentSystems\ConnectumPaymentSystem;
use App\PaymentSystems\CryptoCloudPaymentSystem;
use App\PaymentSystems\EWalletPaymentSystem;
use App\PaymentSystems\FakePaymentSystem;
use App\PaymentSystems\InnerPaymentSystem;
use App\PaymentSystems\PaymorePaymentSystem;
use App\PaymentSystems\PayPalPaymentSystem;
use App\PaymentSystems\PoliPaymentSystem;
use App\PaymentSystems\RevolutPaymentSystem;
use App\PaymentSystems\StripePaymentSystem;
use App\PaymentSystems\StripeRemotePaymentSystem;
use App\PaymentSystems\YooPaymentSystem;
use App\Transaction;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

if (! function_exists('cat_helper')) {

    function cat_helper(): string
    {
        return 'cat_helper';
    }
}

if (! function_exists('auth_header')) {

    function auth_header(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $user->api_token];
    }
}

if (! function_exists('my_user')) {

    function my_user(): ?Model
    {
        $token = request()->bearerToken();
        Log::info("token = " . $token);
        if(! $token) {
            $token = request()->input('api_token');
        }

        return User::where('api_token', $token)->firstOrFail();
    }
}

if (! function_exists('echo_exception')) {

    function echo_exception(\Exception $e, $trace=false)
    {
        echo 'Exception: ' . $e->getMessage() . PHP_EOL;
        echo 'File: ' . $e->getFile() . PHP_EOL;
        echo 'Line: ' . $e->getLine() . PHP_EOL;
        if ($trace) {
            echo 'Stacktrace: ' . $e->getTraceAsString() . PHP_EOL;
        }
    }
}

if (! function_exists('describe_exception')) {

    function describe_exception(\Throwable $e, $trace=false): string
    {
        $str = "Exception: {$e->getMessage()}";
        $str .= " File: {$e->getFile()} Line: {$e->getLine()}";
        if ($trace) {
            $str .= ' Stacktrace: ' . $e->getTraceAsString() . PHP_EOL;
        }
        return $str;
    }
}

if (! function_exists('xtime')) {

    function xtime(callable $f)
    {
        $start = microtime(true);
        $f();
        return microtime(true) - $start;
    }
}

if (! function_exists('scontains')) {

    function scontains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== FALSE;
    }
}

if (! function_exists('avg')) {

    function avg($a, $b): int
    {
        return intval(($a + $b) / 2);
    }
}

if (! function_exists('make_data_getter')) {

    function make_data_getter($data)
    {
        return function($field, $default=null) use ($data) {
            return data_get($data, $field, $default);
        };
    }
}

if (! function_exists('carbon_parse')) {

    function carbon_parse($date): string
    {
        return \Illuminate\Support\Carbon::parse($date, 'Europe/Moscow')->toString();
    }
}

if (! function_exists('maybe_user')) {

    function maybe_user(): ?User
    {
        $headerToken = request()->bearerToken();

        if (empty($headerToken)) {
            return null;
        }

        /** @var \App\User $user */
        return User::where('api_token', $headerToken)->first();
    }
}

if (! function_exists('base64url_encode')) {
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
if (! function_exists('base64url_decode')) {
    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

if (! function_exists('either')) {
    function either() {
        $args = func_get_args();
        foreach($args as $arg) {
            if($arg) {
                return $arg;
            }
        }
        return false;
    }
}

if (!function_exists('get_payment_system_with_default')){
    function get_payment_system_with_default(string $cur, Request $request)
    {
        if($request->has('payment_method_id')){
            $pmService = resolve(\App\Services\PaymentMethodsService::class);

            /**
             * @var \App\Services\PaymentMethodsService $pmService
             */

            return $pmService->getPaymentSystemForMethod($request->input('payment_method_id'));
        }

        return resolve(auto_payment_system_class($cur, $request));
    }
}

if (! function_exists('auto_payment_system_class')) {
    function auto_payment_system_class(string $cur, Request $request): string {
        $avaliblePaymentSystems = [
			BankTransferPaymentSystem::class,
			CardsInternationalPaymentSystem::class,
			CentPaymentSystem::class,
			ConnectumPaymentSystem::class,
			CryptoCloudPaymentSystem::class,
			EWalletPaymentSystem::class,
			PaymorePaymentSystem::class,
			PayPalPaymentSystem::class,
			PoliPaymentSystem::class,
			RevolutPaymentSystem::class,
			StripeRemotePaymentSystem::class,
			YooPaymentSystem::class,
			InnerPaymentSystem::class
		];
        if ($request->has('payment_system') && in_array($request->input('payment_system'), $avaliblePaymentSystems)) {
            return $request->input('payment_system');
        }
        return match (true) {
            boolval(App::environment('testing')) => FakePaymentSystem::class,
            Transaction::CUR_RUB !== $cur,
            'RU' !== $request->country_value     => PaymorePaymentSystem::class,
            default                              => YooPaymentSystem::class,
        };
    }
}
if (! function_exists('auto_payment_system_class_forApp')) {

	function auto_payment_system_class_forApp(string $cur, Request $request): string{
		$avaliblePaymentSystems = [
			StripeRemotePaymentSystem::class,
			YooPaymentSystem::class
		];

		if ($request->has('payment_system')) {
			if( in_array($request->input('payment_system'), $avaliblePaymentSystems) ){
				return $request->input('payment_system');
			}else{
				return '';
			}
        }

		foreach($avaliblePaymentSystems as $paymentSystem){
			if(in_array($cur, resolve($paymentSystem)->getAvailableCurrencies())){
				return $paymentSystem;
			}
		}
		return '';

	}
}
if (! function_exists('bind_fake_suppliers')) {
    function bind_fake_suppliers(){
        App::bind(AEverve::class, EverveFake::class);
        App::bind(ANakrutka::class, NakrutkaFake::class);
        App::bind(ANakrutkaAuto::class, NakrutkaAutoFake::class);
        App::bind(APrm4u::class, Prm4uFake::class);
        App::bind(APrm4uAuto::class, Prm4uAutoFake::class);
        App::bind(ASocgress::class, SocgressFake::class);
        App::bind(AVkserfing::class, VkserfingFake::class);
        App::bind(AVkserfingAuto::class, VkserfingAutoFake::class);
        App::bind(AVtope::class, VtopeFake::class);
	}
}
