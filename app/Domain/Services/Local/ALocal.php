<?php

namespace App\Domain\Services\Local;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class ALocal extends AbstractService implements IDiscoverable
{
    public const TAG = 'LOCAL';
}
