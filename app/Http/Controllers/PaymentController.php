<?php

namespace App\Http\Controllers;

use App;
use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Exceptions\NonReportable\BadCurrencyException;
use App\Payment;
use App\PaymentMethod;
use App\PaymentSystems\CentPaymentSystem;
use App\PaymentSystems\CryptoCloudPaymentSystem;
use App\PaymentSystems\PaymorePaymentSystem;
use App\PaymentSystems\StripePaymentSystem;
use App\PaymentSystems\StripeRemotePaymentSystem;
use App\PaymentSystems\YooPaymentSystem;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\Admin\PaymentSystemsService;
use App\Services\CurrencyService;
use App\Services\Money\PaymentService;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    #[
        Group('payment'),
        Endpoint('status'),
        Verbs(D::GET),
        Role('ROLE_ANY'),
        Text('Информация о платеже'),
        Param('id', required: true, type: D::INT),
    ]
    public function status(Request $request, int $id): ApiResponse
    {
        $currency = resolve(CurrencyService::class);

        /**
         * @var CurrencyService $currency
         */

        $payment = Payment::where('id', $id)->firstOrFail();

        return new ApiSuccess(message: '', data: [
            'status'    => $payment->status,
            'terminal'  => in_array($payment->status, Payment::TERMINAL_STATUSES),
            'total'     => $payment->amount,
            'currency'  => $payment->currency,
            'total_usd' => $currency->convert(from: $payment->currency, to: 'USD', amount: $payment->amount)
        ]);
    }

    #[
        Group('payment'),
        Endpoint('deposit'),
        Verbs(D::POST),
        Role('ROLE_ANY'),
        Text('Пополнить счет из ЛК'),
        Param('amount', true, D::INT),
        Param('success_url', true, D::URL),
        Param('cancel_url', true, D::URL),
    ]
    public function deposit(Request $request, PaymentService $paymentService): ApiResponse {
        $val = $request->validate([
            'amount'      => 'required|numeric',
            'cancel_url'  => 'required|string',
            'description' => 'required|string',
            'success_url' => 'required|string',
        ]);
        $user = Auth::user();
        $paymentSystem = get_payment_system_with_default($user->cur, $request);
        $val['cur'] = $user->cur;

        return new ApiSuccess(
            '',
            $paymentService->create(
                $paymentSystem,
                $user,
                $val['amount'],
                $val,
                [],
                false,
                false,
                $request->ip_value,
            )
        );
    }

    #[
        Group('payment'),
        Endpoint('systems'),
        Verbs(D::GET),
        Role('ROLE_ANY'),
        Text('Информация о классах доступных платежных систем'),
        Param('cur', required: false, type: D::STRING),
    ]
    public function systems(Request $request): ApiResponse
    {
		if ($user = maybe_user()) {
            $cur = $user->cur;
        }
        else {
            $cur = $request->get('cur', $request->currency_value);
        }
        if (! in_array($cur, Transaction::CUR)) {
            throw new BadCurrencyException($cur);
        }
        $isApp  = $request->has('app');
        $locale = $request->get('locale');

        if($isApp && !in_array($locale, config('app.locales'))){
            $locale = 'en';
        }
        $paymentSystems = [
            resolve(CentPaymentSystem::class),
            resolve(PaymorePaymentSystem::class),
            resolve(StripePaymentSystem::class),
			resolve(StripeRemotePaymentSystem::class),
            resolve(YooPaymentSystem::class),
            resolve(CryptoCloudPaymentSystem::class),
        ];
        $avaliblePS = array_filter(
            $paymentSystems,
            fn($paymentSystem) => in_array(
                $cur,
                $paymentSystem->getAvailableCurrencies()
            ) && ($isApp ? $paymentSystem->isUsageForApp() : 1)
        );
        $avaliblePS = array_map(
            function($paymentSystem) use ($locale, $isApp) {
                $array = [
                    'name'      => $paymentSystem->getName(),
                    'curs'      => $paymentSystem->getAvailableCurrencies(),
                    'className' => $paymentSystem::class
                ];
                
                if($isApp && $paymentSystem->isUsageForApp())
                    $array['data'] = $paymentSystem->getPsInfo($locale);
                    
                return $array;
            },
            $avaliblePS
        );
        return new ApiSuccess(message: '', data: $avaliblePS);
    }

    #[
        Group('payment'),
        Endpoint('methods'),
        Verbs(D::GET),
        Role('ROLE_ANY'),
        Text('Информация о классах доступных платежных методов'),
    ]
    public function methods(PaymentSystemsService $psService, Request $request): ApiResponse
    {
        $paymentMethods = [];

        PaymentMethod::where('active_flag', true)->get()->each(function ($item) use (&$paymentMethods, $psService){
            /**
             * @var PaymentMethod $item
             */

            if($item->icon){
                $item->icon = $psService->getIconsBaseDir() . '/' . $item->icon;
            }
            else{
                $item->icon = $psService->getIconsBaseDir() . '/cardRF.svg';
            }

            $paymentMethods[] = $item;
        });

        $data = array(
            'methods' => $paymentMethods,
        );

        return new ApiSuccess(message: '', data: $data);
    }
}
