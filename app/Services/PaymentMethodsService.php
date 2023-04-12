<?php

namespace App\Services;

use App\PaymentMethod;
use App\PaymentSystems\InnerPaymentSystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class PaymentMethodsService
{
    protected array $paymentSystems;

    protected LoggerInterface $log;

    public function __construct(array $paymentSystems)
    {
        $this->paymentSystems = $paymentSystems;

        $this->log = Log::channel('payments');
    }

    public function findPaymentMethod(int $methodId): PaymentMethod
    {
        return PaymentMethod::findOrFail($methodId);
    }

    public function getPaymentSystemForMethod(int $methodId)
    {
        //Pay from balance
        if($methodId === 0){
            return resolve(InnerPaymentSystem::class);
        }

        $method = $this->findPaymentMethod($methodId);

        $paymentSystem = Arr::get($this->paymentSystems, $method->payment_system);

        if(!$paymentSystem){
            throw new \RuntimeException(sprintf("Payment system: [%s] is not found at config", $method->payment_system));
        }

        $this->log->debug('Init payment system for method ...', [
            'methodId'        => $methodId,
            'paymentSystem'   => $paymentSystem
        ]);

        $paymentSystemInstance = resolve($paymentSystem['class']);

        if(Arr::get($paymentSystem, 'isGate')){
            $paymentSystemInstance->setPaymentMethod($method->gate_method_id);
        }

        return $paymentSystemInstance;
    }
}
