<?php

namespace App\Domain\Services\Socgress;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class ASocgress extends AbstractService implements IDiscoverable
{
    public const TAG = 'SOCGRESS';
}
