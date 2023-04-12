<?php

namespace App\Services;

use App\Exceptions\Reportable\ConfigurationException;

class DistributionService
{
    protected $services = [];

    public function all(): array
    {
        return $this->services;
    }

    public function data(array $config): self
    {
        $this->services = collect($config)->filter(function ($params, $index) {
            return $params['isEnabled'] ?? true;
        })->map(function ($params, $index) {
            return [
                'name' => $params['name'] ??  // slot name
                    throw new ConfigurationException('Slot has no name ' . json_encode($params)),
                'order' => $params['order'] ?? 100,
                'min'   => $params['min'] ?? 100,
                'max'   => $params['max'] ?? 200,
                'n'     => 0,
            ];
        })->values()
            ->all();

        return $this;
    }

    public function distribution(int $count): array
    {
        return (new Distributor($this->services))->getDistribution($count);
    }
}
