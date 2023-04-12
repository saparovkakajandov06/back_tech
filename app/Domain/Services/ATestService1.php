<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\IDiscoverable;

abstract class ATestService1 extends AbstractService implements IDiscoverable
{
    public const TAG = 'TEST_1';

    public function doTestMethod1()
    {
    }
}
