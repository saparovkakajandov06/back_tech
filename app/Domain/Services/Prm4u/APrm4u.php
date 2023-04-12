<?php

namespace App\Domain\Services\Prm4u;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class APrm4u extends AbstractService implements IDiscoverable
{
    public const TAG = 'PRM4U';
}
