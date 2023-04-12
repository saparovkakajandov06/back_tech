<?php

namespace App\Domain\Services\Nakrutka;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class ANakrutka extends AbstractService implements IDiscoverable
{
    public const TAG = 'NAKRUTKA';
}
