<?php

namespace App\PaymentSystems;

class PayOpPaymentSystem extends AbstractPayOpPaymentSystem
{
    protected ?string $paymentMethodId = null;

    public function setPaymentMethod(string $methodId)
    {
        $this->paymentMethodId = $methodId;
    }

    protected function getPaymentMethodId(?string $country = null): int
    {
        return $this->paymentMethodId;
    }
}
