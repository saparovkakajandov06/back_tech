<?php

namespace App\Domain\Splitters;

use App\Domain\Models\CompositeOrder;
use App\Services\DistributionService;

abstract class ASplitter implements ISplitter
{
    protected DistributionService $distributionService;

    public function __construct(DistributionService $service)
    {
        $this->distributionService = $service;
    }

    abstract public function split(CompositeOrder $order, array $config): array;

    static public function throw(int $count)
    {
        //
    }
}
