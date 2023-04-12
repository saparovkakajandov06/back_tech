<?php

namespace App\Domain\Services;


use App\Domain\Interfaces\IDiscoverable;

class TestService3 implements IDiscoverable
{
    public const TAG = 'TEST_3';

    public function doTestMethod3()
    {
    }
}
