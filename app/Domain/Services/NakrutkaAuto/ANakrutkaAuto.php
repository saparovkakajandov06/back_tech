<?php

namespace App\Domain\Services\NakrutkaAuto;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class ANakrutkaAuto extends AbstractService implements IDiscoverable
{
    public const TAG = 'NAKRUTKA_AUTO';
}
