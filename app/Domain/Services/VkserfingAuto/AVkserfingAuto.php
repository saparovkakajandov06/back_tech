<?php
namespace App\Domain\Services\VkserfingAuto;

use App\Domain\Interfaces\IDiscoverable;
use App\Domain\Services\AbstractService;

abstract class AVkserfingAuto extends AbstractService implements IDiscoverable
{
    public const TAG = 'VKSERFING_AUTO';
}