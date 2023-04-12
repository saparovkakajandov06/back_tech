<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Controllers;

use App;
use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Payment;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\Money\PaymentService;
use App\Transaction;
use App\User;
use App\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class CompositeOrdersController extends Controller
{
    private bool $isTestMode = false;

    public function __construct(Request $request)
    {
        $this->isTestMode = $request['test'] == 'true';

        if ($this->isTestMode) {
            bind_fake_suppliers();
        }
    }

    public function mainApp(Request $request): ApiResponse
    {
        $request->validate([
            'tag'            => 'required|string',
            'currency_value' => [
                'string',
                Rule::in(Transaction::CUR)
            ],
            'locale'         => [
                'string',
                Rule::in(config('app.locales'))
            ]
        ]);

        $validated = $request->all();
        $input = $request->all();

        /**
         * normal users get premium bonuses, auto users -- don't
         */
        if ($user = maybe_user()) {
            Auth::setUser($user);
        } else {
            $user = User::findOrCreate(
                referralCode: $validated['ref_code'] ?? null,
                token: $validated['auto_token'] ?? null,
                language: $validated['locale'] ?? 'ru',
                currency: $request->currency_value,
            );
        }

        if (($paymentClass = auto_payment_system_class_forApp($user->cur, $request)) == '') {
            return new ApiError("This payment system is unavailable");
        }
        $paymentSystem = resolve($paymentClass);

        if (!$paymentSystem->isUsageForApp()) {
            return new ApiError("This payment system is unavailable for using in app");
        }

        $locale = $request->get('locale', '');

        $service = UserService::where('tag', $validated['tag'])->firstOrFail();
        if (!$service->hasLabel('ENABLED')) {
            return new ApiError("Service temporary unavailable: " . $validated['tag']);
        }
        // TODO: send only order-required data down the line
        /**
         * we actually modify $input here..
         */
        $input['app'] = true;
        $service->processPipeline(params: $input);

        $orderIds = [];
        $totalAmount = 0;
        foreach ($input as $params) {
            /** @var $order CompositeOrder */
            $order = $user->compositeOrders()->create([
                'user_service_id' => $service->id,
                'params'          => $params,
                'session_id'      => $validated['session_id'] ?? null,
            ]);

            $order->refresh(); // load status
            $order->split();

            $orderIds[] = $order->id;
            $totalAmount += $params['cost'];
        }

        $totalAmount = (double)number_format($totalAmount, 2, '.', '');
        if (array_key_exists('description', $validated)) {
            $validated['description'] .= ' ' . ($validated['link'] ?? $validated['login']);
        } else {
            $validated['description'] = 'Опата заказа №' . $orderIds[0];
        }

        $paymentSession = $paymentSystem->startOrderSession([
            'forApp'      => true,
            'amount'      => $totalAmount,
            'cur'         => $user->cur,
            'description' => $validated['description'],
            'locale'      => $locale ?? $user->locale ?? 'en',
            'order_ids'   => $orderIds,
            'user_id'     => $user->id,
            'user_name'   => $user->name,
        ]);

        return new ApiSuccess(message: 'ok, payment expected', data: [
            'auto_token'      => $user->api_token,
            'payment_session' => $paymentSession
        ]);
    }

    /**
     * Create composite order
     */
    #[Endpoint('c_orders')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('order')]
    #[Text('Создание заказа из личного кабинета')]
    #[Param('tag', true, D::STRING, 'тег выбранной услуги', 'VK_LIKES_LK')]
    #[Param('count', true, D::INT)]
    #[Param('link', true, D::URL,
        'наличие параметра зависит от используемого сервиса, обязателен если не используется login')]
    #[Param('login', true, D::STRING,
        'наличие параметра зависит от используемого сервиса, обязателен если не используется link')]
    #[Param('posts', true, D::INT, 'наличие параметра зависит от используемого сервиса')]
    #[Param('success_url', true, D::URL, 'перенаправление, если платеж проведен')]
    #[Param('cancel_url', true, D::URL, 'перенаправление, если платеж не проведен')]
    public function create(Request $request): ApiResponse
    {
        $validator = Validator::make($request->all(), [
            'tag' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new ApiError(__('s.invalid_data'), $validator->errors());
        }

        $input = $request->all(); // from json
        $request->currency_value = Auth::user()->cur;
        $userService = UserService::where('tag', $input['tag'])->firstOrFail();
        if (!$userService->hasLabel('ENABLED')) {
            return new ApiError("Service temporary unavailable: " . $input['tag']);
        }
        $userService->processPipeline($input);
        $orders = [];
        foreach ($input as $params) {

            /** @var $order CompositeOrder */
            $order = Auth::user()->compositeOrders()->create([
                'user_id'         => Auth::user()->id,
                'user_service_id' => $userService->id,
                'params'          => $params,
                'session_id'      => $request->input('session_id') ?? null,
            ]);

            $order->refresh(); // status
            $order->split();

            $order->refresh();
            $order->pay();

            $order->refresh();
            $order->run();

            $order->userService;
            $orders[] = $order;
        }

        return new ApiSuccess('orders created', [
            'orders' => $orders,
        ]);
    }

    #[Endpoint('c_orders/async')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('order')]
    #[Text('Асинхронное создание заказа из личного кабинета')]
    #[Param('tag', true, D::STRING)]
    #[Param('count', true, D::INT)]
    #[Param('link', true, D::URL,
        'наличие параметра зависит от используемого сервиса, обязателен если не используется login')]
    #[Param('login', true, D::STRING,
        'наличие параметра зависит от используемого сервиса, обязателен если не используется link')]
    #[Param('posts', true, D::INT, 'наличие параметра зависит от используемого сервиса')]
    #[Param('success_url', true, D::URL, 'перенаправление, если платеж проведен')]
    #[Param('cancel_url', true, D::URL, 'перенаправление, если платеж не проведен')]
    public function createAsync(Request $request): ApiResponse
    {
        $validator = Validator::make($request->all(), [
            'tag' => 'required|string',
        ]);

        if ($validator->fails()) {
            return new ApiError(__('s.invalid_data'), $validator->errors());
        }

        $input = $request->all(); // from json
        $request->currency_value = Auth::user()->cur;
        $userService = UserService::where('tag', $input['tag'])->firstOrFail();
        if (!$userService->hasLabel('ENABLED')) {
            return new ApiError("Service temporary unavailable: " . $input['tag']);
        }
        $userService->processPipeline($input);

        $orders = [];
        foreach ($input as $params) {
            $uuid = (string)Str::uuid();
            $order = Auth::user()->compositeOrders()->create([
                'user_id'         => Auth::user()->id,
                'user_service_id' => $userService->id,
                'params'          => $params,
                'session_id'      => $request->input('session_id') ?? null,
                'uuid'            => $uuid,
            ]);

            $order->refresh();
            $order->split();

            $order->refresh();
            $order->pay();

            $cmd = "php ../artisan ss:process $uuid &";
            $process = Process::fromShellCommandline($cmd);
            $process->start(); //async

            /*
             * Ожидаем когда скрипт будет запущен.
             *
             * Вызов не успевает запустить скрипт через proc_open.
             * Если скрипт успевает запуститься до ответа бэка на фронт, то фоновая задача корректно запускается.
             * Если сервер отрабатывает слишком быстро, то скрипт не запускается.
            */
            sleep(1);

            //$order->chunks;
            $order->userService;
            $orders[] = $order;
        }

        return new ApiSuccess('orders created async', [
            'orders' => $orders,
        ]);
    }

    #[
        Endpoint('c_orders/main'),
        Verbs(D::POST),
        Group('order'),
        Text('Создание заказа с главной'),
        Param('tag', true, D::STRING),
        Param('count', true, D::INT),
        Param(
            name: 'link',
            required: true,
            type: D::URL,
            descr: 'наличие параметра зависит от используемого сервиса, обязателен если не используется login',
        ),
        Param(
            name: 'login',
            required: true,
            type: D::STRING,
            descr: 'наличие параметра зависит от используемого сервиса, обязателен если не используется link'
        ),
        Param('posts', true, D::INT, 'наличие параметра зависит от используемого сервиса'),
        Param('success_url', true, D::URL, 'перенаправление, если платеж проведен'),
        Param('cancel_url', true, D::URL, 'перенаправление, если платеж не проведен'),
    ]
    public function main(Request $request): ApiResponse
    {
        $request->validate([
            'tag'         => 'required|string',
            'success_url' => 'required|url',
            'cancel_url'  => 'required|url',
        ]);
        $validated = $request->all();
        $input = $request->all();

        /**
         * normal users get premium bonuses, auto users -- don't
         */
        if ($user = maybe_user()) {
            Auth::setUser($user);
        } else {
            $user = User::findOrCreate(
                referralCode: $validated['ref_code'] ?? null,
                token: $validated['auto_token'] ?? null,
                language: $validated['locale'] ?? 'ru',
                currency: $request->currency_value,
            );
        }

        $service = UserService::where('tag', $validated['tag'])->firstOrFail();
        if (!$service->hasLabel('ENABLED')) {
            return new ApiError("Service temporary unavailable: " . $validated['tag']);
        }
        // TODO: send only order-required data down the line
        /**
         * we actually modify $input here
         */
        $service->processPipeline(params: $input);

        $orderIds = [];
        $totalAmount = 0;
        $ua = $request->header('User-Agent');
        foreach ($input as $params) {
            $params['fbp'] = $validated['fbp'] ?? null;
            $params['fbc'] = $validated['fbc'] ?? null;
            $params['ua'] = $ua;

            $params['locale'] = $validated['locale'] ?? $user->lang;

            /** @var $order CompositeOrder */
            $order = $user->compositeOrders()->create([
                'user_service_id' => $service->id,
                'params'          => $params,
                'session_id'      => $validated['session_id'] ?? null,
            ]);

            $order->refresh(); // load status
            $order->split();

            $orderIds[] = $order->id;
            $totalAmount += $params['cost'];
        }

        $totalAmount = (double)number_format($totalAmount, 2, '.', '');
        if (array_key_exists('description', $validated)) {
            $validated['description'] .= ' ' . ($validated['link'] ?? $validated['login']);
        } else {
            $validated['description'] = 'Опата заказа №' . $orderIds[0];
        }
        $paymentSystem = get_payment_system_with_default($user->cur, $request);
        $paymentSession = $paymentSystem->startOrderSession([
            'amount'      => $totalAmount,
            'cancel_url'  => $validated['cancel_url'],
            'cur'         => $request->currency_value,
            'description' => $validated['description'],
            'locale'      => $validated['locale'] ?? 'ru',
            'order_ids'   => $orderIds,
            'success_url' => $validated['success_url'],
            'user_id'     => $user->id,
            'user_name'   => $user->name,
        ]);
        return new ApiSuccess(message: 'ok, payment expected', data: [
            'auto_token'      => $user->api_token,
            'payment_session' => $paymentSession,
        ]);
    }

    #[
        Endpoint('c_orders/pack'),
        Verbs(D::POST),
        Group('order'),
        Text('Создание группы заказов с главной страницы'),
        Param(
            name: 'pack',
            required: true,
            type: D::TYPE_ARRAY,
            descr: 'массив объектов с параметрами заказов',
            example: '[{ tag: INSTAGRAM_LIKES_MAIN, link: https://instagram.com/p/some_hash, count: 100 }, { tag: INSTAGRAM_SUBS_MAIN, login: therock, count: 100 }]'
        ),
        Param('success_url', true, D::URL, 'перенаправление, если платеж проведен'),
        Param('cancel_url', true, D::URL, 'перенаправление, если платеж не проведен'),
    ]
    public function pack(Request $request, PaymentService $paymentService): ApiResponse
    {
        $request->validate([
            'pack' => 'required|array',
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
            'use_balance' => 'required|boolean',
            'use_cashback' => 'required|boolean',
        ]);

        $validated = $request->all();

        /**
         * normal users get premium bonuses, auto users -- don't
         */
        if ($user = maybe_user()) {
            Auth::setUser($user);
        } else {
            $user = User::findOrCreate(
                referralCode: $validated['ref_code'] ?? null,
                token: $validated['auto_token'] ?? null,
                language: $validated['locale'] ?? 'ru',
                currency: $request->currency_value,
            );

            if ($user->cur !== $request->currency_value) {
                $user = User::findOrCreate(
                    referralCode: $user->parent()->first()->ref_code ?? null,
                    language: $validated['locale'] ?? 'ru',
                    currency: $request->currency_value,
                );
            }
        }

        $fbp = $validated['fbp'] ?? null;
        $fbc = $validated['fbc'] ?? null;
        $origin = $validated['origin'] ?? null;
        $ua = $request->header('User-Agent');
        $session_id = $validated['session_id'] ?? null;
        $orderIds = [];
        $orders = [];
        $totalAmount = 0;

        foreach ($validated['pack'] as $input) {
            $service = UserService::where('tag', $input['tag'])->firstOrFail();
            if (!$service->hasLabel('ENABLED')) {
                return new ApiError("Service temporary unavailable: " . $input['tag']);
            }
            /**
             * we actually modify $input here
             */
            $service->processPipeline(params: $input);
            foreach ($input as $params) {
                $params['fbp'] = $fbp;
                $params['fbc'] = $fbc;
                $params['ua'] = $ua;
                $params['origin'] = $origin;

                $params['locale'] = $validated['locale'] ?? $user->lang;
                $params['is_test_mode'] = $this->isTestMode;

                /** @var $order CompositeOrder */
                $order = $user->compositeOrders()->create([
                    'user_service_id' => $service->id,
                    'params'          => $params,
                    'session_id'      => $session_id,
                ]);

                $order->refresh(); // load status
                $order->split();

                $orderIds[] = $order->id;
                $orders[] = $order;
                $totalAmount += $params['cost'];
            }
        }
        $totalAmount = (double)number_format($totalAmount, 2, '.', '');
        if (!array_key_exists('description', $validated)) {
            $validated['description'] = 'Опата заказа №' . $orderIds[0];
        }

        $paymentSystem = get_payment_system_with_default($user->cur, $request);

        $arr = $paymentService->create($paymentSystem, $user, $totalAmount, [
            'cancel_url'  => $validated['cancel_url'],
            'description' => $validated['description'],
            'locale'      => $validated['locale'] ?? 'ru',
            'success_url' => $validated['success_url'],
        ], $orders, $request->use_balance, $request->use_cashback, $request->ip_value);

        return new ApiSuccess(message: 'ok, payment expected', data: [
            'auto_token'      => $user->api_token,
            'payment_session' => [
                'url' => $arr['url'],
                'id'  => $arr['payment']->id
            ],
        ]);
    }

    #[
        Endpoint('c_orders/repay'),
        Verbs(D::POST),
        Group('order'),
        Text('Переоплата группы заказов с главной страницы'),
        Param('payment_id', true, D::URL, 'Id локального платежа'),
        Param('payment_system', true, D::STRING, 'платежная система'),
        Param('success_url', true, D::URL, 'перенаправление, если платеж проведен'),
        Param('cancel_url', true, D::URL, 'перенаправление, если платеж не проведен'),
    ]
    public function repay(Request $request, PaymentService $paymentService): ApiResponse
    {
        $request->validate([
            'payment_id'     => 'required|exists:payments,id',
            'payment_method_id' => 'required|int',
            'success_url'    => 'required|url',
            'cancel_url'     => 'required|url',
            'use_balance'    => 'required|boolean',
            'use_cashback'   => 'required|boolean',
        ]);
        $validated = $request->all();

        $payment = Payment::findOrFail($validated['payment_id']);

        if (in_array($payment->status, Payment::TERMINAL_STATUSES)) {
            return new ApiError('Payment has terminal status', []);
        }

        $orders = CompositeOrder::whereIn('id', $payment->order_ids)->get()->all();

        if ($user = maybe_user()) {
            Auth::setUser($user);
        }

        $paymentSystem = get_payment_system_with_default($payment->currency, $request);

        $arr = $paymentService->create($paymentSystem, $payment->user, $payment->amount, [
            'cancel_url'  => $validated['cancel_url'],
            'description' => $payment['description'],
            'locale'      => $validated['locale'] ?? 'ru',
            'success_url' => $validated['success_url'],
        ], $orders, $request->use_balance, $request->use_cashback, $request->ip_value);

        return new ApiSuccess(message: 'ok, payment expected', data: [
            'auto_token' => $user->api_token ?? null,
            'payment_session' => [
                'url' => $arr['url'],
                'id' => $arr['payment']->id
            ],
        ]);
    }

    #[Endpoint('c_orders/{id}')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MODERATOR')]
    #[Group('order')]
    #[Text('Информация о заказе')]
    #[Param('id', true, D::INT)]
    public function show($id)
    {
        $compositeOrder = CompositeOrder::findOrFail($id);
        $compositeOrder->chunks;
        return $compositeOrder;
    }

    #[Endpoint('c_orders/{action}')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MANAGER')]
    #[Group('order')]
    #[Text('Метод для Администратора')]
    #[Text('Изменить статус заказа')]
    #[Param('action', true, D::STRING, 'mod_run, mod_stop')]
    public function changeOrderStatus(Request $request, string $action): ApiResponse
    {
        if (!in_array($action, [
            'mod_run',
            'mod_stop',
            'mod_cancel',
            'mod_complete',
            'mod_complete_main',
            'mod_update',
        ])) {
            return new ApiError("Bad moderator action: $action");
        }
        $method = Str::camel($action);
        if (!$request->orders) {
            return new ApiError("No orders given to $action");
        }
        if ('modUpdate' !== $method) { // no network requests, no async operations, no timeouts
            foreach (explode(' ', $request->orders) as $id) {
                $order = CompositeOrder::findOrFail($id);
                $order->$method();
            }
            return new ApiSuccess('success');
        }
        // something more complex
        $cmd = "php ../artisan order_update:manual {$request->orders}" . PHP_EOL;

        $process = Process::fromShellCommandline($cmd);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        return new ApiSuccess('OK', [
            'cmd'    => $cmd,
            'output' => $output,
        ]);
    }

    #[Endpoint('c_orders/uuid/{uuid}')]
    #[Verbs(D::GET)]
    #[Group('order')]
    #[Text('Получить заказ по uuid')]
    #[Param('uuid', true, D::STRING)]
    public function showByUUID($uuid)
    {
        $order = CompositeOrder::where('uuid', $uuid)->firstOrFail();

        return [
            'order' => $order,
        ];
    }

    #[Endpoint('c_orders/{id}/logs')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MODERATOR')]
    #[Group('order')]
    #[Text('Метод для Администратора')]
    #[Text('Получить лог заказа')]
    #[Param('id', true, D::INT)]
    public function OLogs($id): ApiResponse
    {
        $order = CompositeOrder::findOrFail($id);

        return new ApiSuccess('order logs', $order->ologs);
    }

    public function updateCommand($id): ApiResponse
    {
        $timeout = 60;

        $order = CompositeOrder::find($id);
        if (!$order) {
            return new ApiError('Order not found');
        }

        if (!in_array($order->status, [
            Order::STATUS_RUNNING,
            Order::STATUS_PAUSED,
            Order::STATUS_ERROR,
        ])) {
            return new ApiError("Order is not running");
        }

        //        Log::info("Now running updateCommand() id = $id");

        $cmd = "php ../artisan x:mass_update $timeout $id\n";

        $process = Process::fromShellCommandline($cmd);
        //        $process->start();
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        return new ApiSuccess('OK', [
            'cmd'    => $cmd,
            'output' => $output,
        ]);
    }
}
