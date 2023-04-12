<?php

namespace App\Domain\Services\Vtope;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class AVtope extends AbstractService implements IDiscoverable
{
    public const TAG = 'VTOPE';
}
