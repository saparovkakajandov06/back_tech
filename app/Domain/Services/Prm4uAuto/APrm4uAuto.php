<?php

namespace App\Domain\Services\Prm4uAuto;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class APrm4uAuto extends AbstractService implements IDiscoverable
{
    public const TAG = 'PRM4U_AUTO';
}
