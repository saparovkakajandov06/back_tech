<?php

namespace App\PaymentSystems;

use App\Domain\Models\CompositeOrder;
use App\Exceptions\NonReportable\BadCurrencyException;
use App\Exceptions\Reportable\ReportableException;
use App\Payment;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Services\Money\PaymentService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class BasePaymentSystem
{
    protected string $name = 'Base';

    protected bool $debug = false;

    protected LoggerInterface $log;

    public function __construct()
    {
        $this->log = Log::channel('payments');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPsInfo($locale): array
    {
        $icon = 'app/payment_systems/';
        try {
            $icon .= $this::ICON_FOR_APP;
        } catch (\Throwable $e) {
            $icon .= "default.svg";
        }
        return [
            'icon' => Storage::url($icon),
            'title' => trans("payment.titlesPs." . $this->name)
        ];
    }

    public function isUsageForApp(): bool
    {
        try {
            return $this::USE_FOR_APP;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function hasCurrency(string $cur): bool
    {
        return in_array($cur, $this->getAvailableCurrencies());
    }

    public function getAvailableCurrencies(): array
    {
        return $this->availableCurrencies;
    }

    public function startAuthSession(array $val): ApiResponse
    {
        $user = Auth::user();
        $val['user_id'] = $user->id;
        $val['locale'] = $user->lang;
        $val['user_name'] = $user->name;
        if (array_key_exists('description', $val)) {
            $val['description'] .= ' ';
        } else {
            $val['description'] = '';
        }
        $val['description'] .= $user->name ?? $user->email;
        return new ApiSuccess(
            message: '',
            data: $this->startOrderSession($val)
        );
    }

    public function startOrderSession(array $val): array
    {
        $val['cur'] = Str::upper($val['cur']);
        if (!in_array($val['cur'], $this->availableCurrencies)) {
            throw new BadCurrencyException();
        }

        $user = User::findOrFail($val['user_id']);
        $orders = CompositeOrder::whereIn('id', $val['order_ids'])->get()->all();


        $payment = app(PaymentService::class)->create($this, $user, $val['amount'], $val, $orders);

        return [
            'url' => $payment['url'],
            'id' => $payment['payment']->id
        ];
    }

    public function handleHook(Request $request)
    {
        try {
            $this->checkSignature($request);
            $status = $this->mapRequestToStatus($request);
            if ($paymentForeignId = $this->getForeignPaymentId($request)) {
                $payment = Payment::where('foreign_id', $paymentForeignId)->firstOrFail();
                /**
                 * TODO: rewrite to Payment model method sometime in the future
                 */
                if (!$this->tryUpdatePayment($payment, $status)) {
                    $this->log->info('Request ' . json_encode($request->all()));
                }
            }
            return $this->getDefaultResponse();
        } catch (\Throwable $e) {
            throw (new ReportableException('Payment hook error'))
                ->withData(['exception' => describe_exception($e, true)]);
        }
    }

    public function checkSignature(Request $request): void
    {
        return;
    }

    protected function tryUpdatePayment(Payment $payment, string $status): bool
    {
        return app(PaymentService::class)->updateStatus($payment, $status);
    }

    public function getDefaultResponse(): mixed
    {
        return response('', 200);
    }

    protected function getRemoteDescription(array $val, Payment $localPayment): string
    {
        return substr($val['description'] ?? '', 0, 250);
    }

    protected function debugLog(string $message, array $context = [])
    {
        if (!$this->debug) {
            return;
        }

        $this->log->debug($message, $context);
    }

    protected function dataGet(array $data, string $key, string $dataType = 'api hook'): string
    {
        $val = data_get($data, $key);

        if (!$val) {
            $pspName = $this->name;

            throw (new ReportableException("Param ${key} is not found at ${pspName} ${dataType}"))
                ->withData(['exception' => json_encode($data)]);
        }

        return $val;
    }
}
