<?php

namespace App\Domain\Services\Vkserfing;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class AVkserfing extends AbstractService implements IDiscoverable
{
    public const TAG = 'VKSERFING';
}
