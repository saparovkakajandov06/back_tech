<?php

namespace App\Domain\Services\Fake;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class AFake extends AbstractService implements IDiscoverable
{
    public const TAG = 'FAKE';
}
