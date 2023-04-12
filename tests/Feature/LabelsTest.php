<?php

namespace Tests\Feature;

use App\Exceptions\NonReportable\NonReportableException;
use App\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;
use US;

class LabelsTest extends TestCase
{
    use DatabaseMigrations;

    public $admin;
    public $user;

    public function setUp(): void
    {
        parent::setUp();
        TFHelpers::runTestSeeders();
    }

    public function testReplace()
    {
        $us = UserService::where('tag', US::INSTAGRAM_LIKES_LK)->firstOrFail();
        $us->labels = [
            "TYPE_LIKES",
            "DISCOUNT_LIKES",
            "VISIBLE",
            "ENABLED",
            "CLIENT_LK"
        ];

        $us->replaceLabel("TYPE_LIKES", "TYPE_DISLIKES");
        $us->replaceLabel("VISIBLE", "INVISIBLE");
        $us->replaceLabel("CLIENT_LK", "CLIENT_MAIN");

        $us->save();
        $us->refresh();

        $this->assertEquals($us->labels, [
          "TYPE_DISLIKES",
          "DISCOUNT_LIKES",
          "INVISIBLE",
          "ENABLED",
          "CLIENT_MAIN"
        ]);
    }

    public function testException()
    {
        $us = UserService::where('tag', US::INSTAGRAM_LIKES_LK)->firstOrFail();
        $us->labels = [ "LABEL_ONE" ];

        $this->expectException(NonReportableException::class);
        $us->replaceLabel("WRONG_LABEL", "");
    }
}
