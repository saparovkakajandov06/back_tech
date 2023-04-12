<?php

namespace App\Domain\Services\Everve;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class AEverve extends AbstractService implements IDiscoverable
{
    public const TAG = 'EVERVE';
}
