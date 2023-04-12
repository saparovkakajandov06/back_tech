<?php

namespace Tests\Feature;

use App\Domain\Services\ATestService1;
use App\Services\ServicesService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ServicesServiceTest extends TestCase
{
    use DatabaseMigrations;

    public ServicesService $ss;

    public function setUp(): void
    {
        parent::setUp();
        $this->ss = new ServicesService();
    }

    public function testLoadsClasses()
    {
        $this->assertNotEmpty($this->ss->getServices());
    }

    public function testFindsMethodInDiscoverableClass()
    {
        $found = $this->ss->getServicesWithMethod('doTestMethod1');

        $this->assertEquals(ATestService1::class, $found[0]);
    }

    public function testDoesNotFindMethodInNonDiscoverableClass()
    {
        $found = $this->ss->getServicesWithMethod('doTestMethod2');

        $this->assertEmpty($found);
    }

    public function testDoesNotFindMethodInNotAbstractClass()
    {
        $found = $this->ss->getServicesWithMethod('doTestMethod3');

        $this->assertEmpty($found);
    }
}
