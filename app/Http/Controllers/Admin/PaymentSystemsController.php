<?php

namespace App\Http\Controllers\Admin;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Param;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Http\Controllers\Controller;
use App\PaymentMethod;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\Admin\PaymentMethodIconsService;
use App\Services\Admin\PaymentSystemsService;
use App\Transaction;
use Illuminate\Http\Request;

class PaymentSystemsController extends Controller
{

    // Find user by name or email
    // for tests
    #[Endpoint('admin/payment-systems/data')]
    #[Verbs(D::GET)]
    #[Role('ROLE_MANAGER')]
    #[Text('Метод для Администратора')]
    #[Text('Получение данных для работы компонента управления платежными методами')]
    public function data(PaymentSystemsService $psService): ApiResponse
    {
        $paymentMethods = [];

        PaymentMethod::all()->each(function ($item) use (&$paymentMethods) {
            $paymentMethods[$item->id] = $item;
        });

        $data = array(
            'methods' => $paymentMethods,
            'availableCountries' => $psService->getAvailableCountries(),
            'availableCurrencies' => Transaction::CUR,
            'paymentSystems' => $psService->getAvailablePaymentSystems(),
            'iconsBaseDir' => $psService->getIconsBaseDir()
        );

        return new ApiSuccess('Платежные системы', $data);
    }

    #[Endpoint('admin/payment-systems/method')]
    #[Verbs(D::DELETE)]
    #[Role('ROLE_MANAGER')]
    #[Text('Метод для Администратора')]
    #[Text('Удаления метода оплаты по ID')]
    #[Param('methodId', false, D::INT, 'обязательный параметр если не передается почта юзера')]
    public function deletePaymentMethod(int $methodId): ApiResponse
    {
        $method = PaymentMethod::find($methodId);

        if (!$method) {
            return new ApiError('Платежный метод не найден');
        }

        $method->delete();

        return new ApiSuccess('ok', [
            'methodId' => $methodId,
        ]);
    }

    #[Endpoint('admin/payment-systems/method')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MANAGER')]
    #[Text('Метод для Администратора')]
    #[Text('Добавление нового метода оплаты')]
    public function createPaymentMethod(Request $request, PaymentMethodIconsService $psIconService): ApiResponse
    {
        $validate = $this->paymentMethodValidateData();
        $validate['icon_uuid'] = 'nullable:string';

        $val = $request->validate($validate);

        if ($val['icon_uuid'] && !$psIconService->hasTmpIcon($val['icon_uuid'])) {
            return new ApiError('Иконка для сохранения в платежный метод не найденна');
        }

        //Fix postgress autoincrement
        $val['id'] = PaymentMethod::max('id') + 1;

        $val['active_flag'] = true;

        $method = PaymentMethod::create($val);

        if ($val['icon_uuid']) {
            $icon = $psIconService->saveIcon($val['icon_uuid'], $method->id);
        } else {
            $icon = $psIconService->defaultIcon();
        }

        $method->icon = $icon;
        $method->saveOrFail();

        $psIconService->clearTmp();

        return new ApiSuccess('success', [
            'methodId' => $method->id,
            'method' => $method->toArray(),
        ]);
    }

    protected function paymentMethodValidateData(): array
    {
        return [
            'order' => 'required:integer',
            'titles' => 'required:array',
            'currencies' => 'required:array',
            'limits' => 'required:array',
            'countries' => 'array',
            'payment_system' => 'required:string',
            'gate_method_id' => 'nullable:string',
            'country_filter' => 'required:string',
            'show_agreement_flag' => 'boolean',
            'active_flag' => 'boolean',
        ];
    }

    #[Endpoint('admin/payment-systems/method')]
    #[Verbs(D::PUT)]
    #[Role('ROLE_MANAGER')]
    #[Text('Метод для Администратора')]
    #[Text('Редактирование метода оплаты')]
    public function updatePaymentMethod(Request $request, PaymentMethodIconsService $psIconService): ApiResponse
    {
        $validate = $this->paymentMethodValidateData();
        $validate['id'] = 'required:integer';
        $validate['icon_uuid'] = 'nullable:string';

        $val = $request->validate($validate);

        $method = PaymentMethod::find($val['id']);

        if (!$method) {
            return new ApiError('Платежный метод не найден');
        }

        $method->fill($val);
        $method->saveOrFail();

        if ($val['icon_uuid'] && $psIconService->hasTmpIcon($val['icon_uuid'])) {
            $icon = $psIconService->saveIcon($val['icon_uuid'], $method->id);

            $method->icon = $icon;
            $method->saveOrFail();
        }

        $psIconService->clearTmp();

        return new ApiSuccess('success', [
            'method' => $method,
        ]);
    }

    #[Endpoint('admin/payment-systems/orders')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MANAGER')]
    #[Text('Метод для Администратора')]
    #[Text('Изменение порядка сортировки методов оплаты')]
    public function changePaymentMethodOrders(Request $request): ApiResponse
    {
        $val = $request->validate([
            'orders' => 'required:array'
        ]);

        foreach ($val['orders'] as $order) {
            try {
                $method = PaymentMethod::findOrFail($order['id']);
                $method->order = $order['order'];
                $method->saveOrFail();
            } catch (\Throwable $e) {
                throw new \RuntimeException(
                    "PaymentMethod: [" . $order['id'] . "] order save error: " . $e->getMessage(), 0, $e
                );
            }
        }

        return new ApiSuccess('success', [
            'orders' => $val['orders'],
        ]);
    }

    #[Endpoint('admin/payment-systems/method/uploadTmpIcon')]
    #[Verbs(D::POST)]
    #[Role('ROLE_MANAGER')]
    #[Text('Метод для Администратора')]
    #[Text('Загрузка иконки метода оплаты')]
    public function uploadTmpPaymentMethodIcon(PaymentMethodIconsService $psIconService, Request $request)
    {
        $request->validate([
            'icon' => 'required|file|mimes:svg'
        ]);

        $uuid = $psIconService->uploadIcon($request->icon);

        return new ApiSuccess('success', [
            'uuid' => $uuid,
            'icon' => $psIconService->buildTmpIconPath($uuid)
        ]);
    }
}
