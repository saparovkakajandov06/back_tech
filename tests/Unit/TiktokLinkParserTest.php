<?php

namespace Tests\Unit;

use App\Parsers\TiktokLinkParser;
use PHPUnit\Framework\TestCase;

class TiktokLinkParserTest extends TestCase {

    private TiktokLinkParser $lp;

    public function setUp(): void
    {
        parent::setUp();
        $this->lp = new TiktokLinkParser();
    }

    /**
     * @dataProvider tiktokLogins
     */
    public function testTiktokLogin($input)
    {
        $this->assertEquals('buzova86', $this->lp->tiktokLogin($input));
    }

    /**
     * @dataProvider tiktokLinks
     */
    public function testTiktokLink($input)
    {
        $correct = 'https://www.tiktok.com/@buzova86/video/123123';
        $this->assertEquals($correct, $this->lp->tiktokLink($input));
    }

    public function testParseTiktokLinkWithQuery()
    {
        $link = "https://www.tiktok.com/@rina.fazira__/video/6970921636656598274?is_from_webapp=v1";
        $parsed = $this->lp->tiktokLink($link);
        $this->assertEquals(
            'https://www.tiktok.com/@rina.fazira__/video/6970921636656598274',
            $parsed
        );
    }

    public function tiktokLogins(): array
    {
        return [
            ['https://www.tiktok.com/buzova86'],
            ['http://www.tiktok.com/buzova86'],
            ['https://www.tiktok.com/@buzova86'],
            ['http://www.tiktok.com/@buzova86'],
            ['https://tiktok.com/buzova86'],
            ['http://tiktok.com/buzova86'],
            ['https://tiktok.com/@buzova86'],
            ['http://tiktok.com/@buzova86'],
            ['www.tiktok.com/buzova86'],
            ['www.tiktok.com/buzova86'],
            ['www.tiktok.com/@buzova86'],
            ['www.tiktok.com/@buzova86'],
            ['tiktok.com/buzova86'],
            ['tiktok.com/buzova86'],
            ['tiktok.com/@buzova86'],
            ['tiktok.com/@buzova86'],

            ['https://tiktok.com/buzova86/something'],
            ['https://tiktok.com/buzova86/a/b/c'],
            ['https://tiktok.com/buzova86?query=1'],
            ['https://tiktok.com/buzova86?query=1/abc'],
            ['tiktok.com/buzova86/something'],
            ['tiktok.com/buzova86/a/b/c'],
            ['tiktok.com/buzova86?query=1/abc'],

            ['https://tiktok.com/@buzova86/something'],
            ['https://tiktok.com/@buzova86/a/b/c'],
            ['https://tiktok.com/@buzova86?query=1'],
            ['https://tiktok.com/@buzova86?query=1/abc'],
            ['tiktok.com/@buzova86/something'],
            ['tiktok.com/@buzova86/a/b/c'],
            ['tiktok.com/@buzova86?query=1/abc'],
        ];
    }

    public function tiktokLinks(): array
    {
        return [
            ['http://www.tiktok.com/@buzova86/video/123123'],
            ['https://www.tiktok.com/buzova86/video/123123'],
            ['www.tiktok.com/buzova86/video/123123'],
            ['www.tiktok.com/@buzova86/video/123123'],
        ];
    }
}