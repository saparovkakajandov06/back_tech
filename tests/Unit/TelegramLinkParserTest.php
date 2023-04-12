<?php

namespace Tests\Unit;

use App\Parsers\TelegramLinkParser;
use PHPUnit\Framework\TestCase;

class TelegramLinkParserTest extends TestCase {

    //php artisan test --filter TelegramLinkParserTest
    private TelegramLinkParser $lp;

    public function setUp(): void
    {
        parent::setUp();
        $this->lp = new TelegramLinkParser();
    }

    /**
     * @dataProvider loginDataProviderPositive
     */
    public function testTelegramLoginPositive($input, $login)
    {
        $this->assertEquals($login, $this->lp->login($input));
    }

    /**
     * @dataProvider loginDataProviderNegative
     */
    public function testTelegramLoginNegative($input, $login)
    {
        $this->assertEquals($login, $this->lp->login($input));
    }

    /**
     * @dataProvider linkDataProviderPositive
     */
    public function testTelegramLinkPositive($input, $login)
    {
        $this->assertEquals($login, $this->lp->link($input));
    }

    /**
     * @dataProvider linkDataProviderNegative
     */
    public function testTelegramLinkNegative($input, $login)
    {
        $this->assertEquals($login, $this->lp->link($input));
    }

    public function loginDataProviderPositive(): array
    {
        return [
            [
                'l_qweqw_ewq_e_w12_23_ew_wWDAWDAW',
                'l_qweqw_ewq_e_w12_23_ew_wWDAWDAW'
            ],
            [
                '@awdwdaw',
                'awdwdaw'
            ],
            [
                '@awdwdaw_123',
                'awdwdaw_123'
            ],
            [
                'awdwda_3_2w_123',
                'awdwda_3_2w_123'
            ],
            [
                '@l_qweqw_ewq_e_w12_23_ew_wWDAWDAW',
                'l_qweqw_ewq_e_w12_23_ew_wWDAWDAW'
            ],
            [
                "@l_1_5",
                "l_1_5"
            ],
            [
                "https://t.me/l_qweqw_ewq",
                'l_qweqw_ewq'
            ],
            [
                't.me/l_qweqw_ewq123',
                'l_qweqw_ewq123'
            ],
        ];
    }

    public function linkDataProviderPositive(): array
    {
        return [
            [
                "https://t.me/l_qweqw_ewq/312312",
                'l_qweqw_ewq/312312'
            ],
            [
                't.me/l_qweqw_ewq123_1/31223',
                'l_qweqw_ewq123_1/31223'
            ],
            [
                "https://t.me/l_qweq/312312",
                'l_qweq/312312'
            ],
            [
                't.me/l_qwe1/31223',
                'l_qwe1/31223'
            ],
        ];
    }

    public function linkDataProviderNegative(): array
    {
        return [
            [
                "https://t.me/l_qweqw_ewq/3123_12",
                null
            ],
            [
                't.me/l_qweqw_ewq123_1_/31223',
                null
            ],
            [
                "https://t.me/1_qweqw_ewq/3123_12",
                null
            ],
            [
                't.me/l_qweqw_ewq123__1/31223',
                null
            ],
            [
                't.me/_l_qweqw_ewq123__1/31223',
                null
            ],
        ];
    }

    public function loginDataProviderNegative(): array
    {
        return [
            [
                '@_awdwdaw_123',
                null
            ],
            [
                '@1awdwdaw',
                null
            ],
            [
                '1_awdwdaw_123',
                null
            ],
            [
                'awdwda_3_2w_123_',
                null
            ],
            [
                '@awdwda_3@_2w_123',
                null
            ],
            [
                '@awdwda_3@_2w_123',
                null
            ],
            [
                '@l_qweqw_ewq_e_w12_23_ew_wWDAWDAWDAWDAWDA',
                null
            ],
            [
                '@lent',
                null
            ],
            [
                'lent',
                null
            ],
            [
                'https://t.me/1l_qweqw_ewq',
                null
            ],
            [
                'https://t.me/l_qweqw_ewq_',
                null
            ],
            [
                't.me/1l_qweqw_ewq123',
                null
            ],
            [
                't.me/l_qweqw___ewq123',
                null
            ],
            [
                'awdw__wda',
                null
            ],
            [
                '@awdw__wda',
                null
            ]
        ];
    }
}
