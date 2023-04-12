<?php

namespace Tests\Unit;

use App\Parsers\IgLinkParser;
use PHPUnit\Framework\TestCase;

class IgLinkParserTest extends TestCase {

    private IgLinkParser $lp;

    public function setUp(): void
    {
        parent::setUp();
        $this->lp = new IgLinkParser();
    }

    /**
     * @dataProvider loginDataProvider
     */
    public function testIgLogin($input, $login)
    {
        $this->assertEquals($login, $this->lp->login($input));
    }

    /**
     * @dataProvider linkDataProvider
     */
    public function testIgLink($input, $correct)
    {
        $this->assertEquals($correct, $this->lp->link($input));
    }

    public function loginDataProvider(): array
    {
        return [
            [
              'https://instagram.com/ab',
              null,
            ],
            [
                'https://instagram.es/#',
                null,
            ],
            [
              'some bad long long login 1234567890 and more text here la la la',
              null,
            ],
            [
              '123@!',
              null,
            ],
            [
                'ab',
                null,
            ],
            [
                'hello',
                'hello',
            ],
            [
                'https://instagram.com/oleg',
                'oleg',
            ],
            [
                'https://instagram.org/login',
                'login',
            ],
            [
                'http://instagram.it/it_login',
                'it_login',
            ],
            [
                'https://www.instagram.com/reel/CQ-npoaIql8/?utm_source=ig_web_copy_link',
                null,
            ],
            [
                'https://www.instagram.com/p/CCITXoUI1-L/?igshid=qe1afz9gu3xv',
                null,
            ],
            [
                'https://www.instagram.com/p/CCMgCiBlrk5EhOvKa_zuhQUg01vtaJQgvtoOic0/?igshid=1u87l0kerxjp7',
                null,
            ],
            [
                'https://www.instagram.com/tv/CCLl2jQqz_7/?igshid=cntxbe4g3jwq',
                null,
            ],
            [
                'https://www.instagram.com/tv/CCNcxzuqqae3Q-lzD5atI6OZpR4bsemAtabZFg0/?igshid=1sqjk5v6ybgqa',
                null,
            ],
            [
                'https://instagram.com/stories/bayanistka2dubl/2345082404414831604?igshid=1a1q7ft0oug1h',
                'bayanistka2dubl',
            ],
            [
                'https://instagram.com/stories/mari_gtn/2345131676471710104?utm_source=ig_story_item_share&igshid=xyf00j00u99q',
                'mari_gtn',
            ],
            [
                'http://instagram.com/oleg',
                'oleg',
            ],
            [
                'https://instagram.com/oleg',
                'oleg',
            ],
            [
                'https://instagram.com/stories/qatest_qatest/2491659192629566320?utm_source=ig_story_item*share&igshid=1a1kyvdem6pch',
                'qatest_qatest',
            ],
            [
                'instagram.com.hk/qatest_qatest',
                'qatest_qatest',
            ],
            [
                'instagram.com.hk/QaTeSt_QATEST',
                'QaTeSt_QATEST',
            ],
            [
                'iNsTagRam.com/QaTeSt_QATEST',
                'QaTeSt_QATEST',
            ],
            [
                'iNstagram.com.hk/qatest_qatest',
                'qatest_qatest',
            ],
            [
                'iNSTagRaM.com.hk/qatest_qatest',
                'qatest_qatest',
            ],
            [
                'iNSTagram.Com.hk/Qatest_qatest',
                null,
            ]
        ];
    }

    public function linkDataProvider(): array
    {
        return [
            [
                'https://instagram.com/oleg',
                'https://instagram.com/oleg'
            ],
            [
                'https://www.instagram.com/reel/CQ-npoaIql8/?utm_source=ig_web_copy_link',
                'https://www.instagram.com/reel/CQ-npoaIql8/'
            ],
            [
                'https://www.instagram.com/p/CCITXoUI1-L/?igshid=qe1afz9gu3xv',
                'https://www.instagram.com/p/CCITXoUI1-L/',
            ],
            [
                'https://www.instagram.com/p/CCMgCiBlrk5EhOvKa_zuhQUg01vtaJQgvtoOic0/?igshid=1u87l0kerxjp7',
                'https://www.instagram.com/p/CCMgCiBlrk5EhOvKa_zuhQUg01vtaJQgvtoOic0/'
            ],
            [
                'https://www.instagram.com/tv/CCLl2jQqz_7/?igshid=cntxbe4g3jwq',
                'https://www.instagram.com/tv/CCLl2jQqz_7/',
            ],
            [
                'https://www.instagram.com/tv/CCNcxzuqqae3Q-lzD5atI6OZpR4bsemAtabZFg0/?igshid=1sqjk5v6ybgqa',
                'https://www.instagram.com/tv/CCNcxzuqqae3Q-lzD5atI6OZpR4bsemAtabZFg0/',
            ],
            [
                'https://instagram.com/stories/bayanistka2dubl/2345082404414831604?igshid=1a1q7ft0oug1h',
                'https://instagram.com/stories/bayanistka2dubl/2345082404414831604',
            ],
            [
                'https://instagram.com/stories/mari_gtn/2345131676471710104?utm_source=ig_story_item_share&igshid=xyf00j00u99q',
                'https://instagram.com/stories/mari_gtn/2345131676471710104',
            ],
            [
                'http://instagram.com/oleg',
                'http://instagram.com/oleg',
            ],
            [
                'https://www.instagram.com/oleg',
                'https://www.instagram.com/oleg',
            ],
        ];
    }
}
