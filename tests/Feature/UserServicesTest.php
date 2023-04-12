<?php

namespace Tests\Feature;

use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class UserServicesTest extends TestCase
{
    use DatabaseMigrations;

    public $us;

    public function setUp(): void
    {
        parent::setUp();
//        TFHelpers::runCommonSeeders();
        TFHelpers::runTestSeeders();

        $us = UserService::first();
        $us->update([
            'labels' => ['one', 'two'],
        ]);
        $this->us = $us;
    }

    public function testHasLabel()
    {
        $this->assertTrue($this->us->hasLabel('one'));
        $this->assertTrue($this->us->hasLabel('two'));
        $this->assertFalse($this->us->hasLabel(''));
        $this->assertFalse($this->us->hasLabel(Str::random(8)));
    }

    public function testAddLabel()
    {
        $label = "label_" . Str::random(8);
        $this->assertFalse($this->us->hasLabel($label));
        $this->us->addLabel($label);
        $this->assertTrue($this->us->hasLabel($label));
    }

    public function testRemoveLabel()
    {
        $label = "label_" . Str::random(8);
        $this->us->addLabel($label);
        $this->assertTrue($this->us->hasLabel($label));
        $this->us->removeLabel($label);
        $this->assertFalse($this->us->hasLabel($label));
    }
}
