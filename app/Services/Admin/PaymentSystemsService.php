<?php

namespace App\Services\Admin;

class PaymentSystemsService
{
    protected array $paymentSystems;

    protected array $countries;

    protected string $iconsBaseDir;

    public function __construct(array $paymentSystems, string $iconsBaseDir, array $countries)
    {
        $this->paymentSystems = $paymentSystems;
        $this->iconsBaseDir = $iconsBaseDir;
        $this->countries = $countries;
    }

    public function getAvailablePaymentSystems(): array
    {
        return $this->paymentSystems;
    }

    public function getAvailableCountries(): array
    {
        return $this->countries;
    }

    public function getIconsBaseDir(): string
    {
        return $this->iconsBaseDir;
    }
}
