<?php

namespace Tests\Unit;

use App\Parsers\VkLinkParser;
use PHPUnit\Framework\TestCase;

class VkLinkParserTest extends TestCase {

    private VkLinkParser $lp;

    public function setUp(): void
    {
        parent::setUp();
        $this->lp = new VkLinkParser();
    }

    /**
     * @dataProvider loginDataProvider
     */
    public function testVkLogin($input, $login)
    {
        $this->assertEquals($login, $this->lp->login($input));
    }

    /**
     * @dataProvider linkDataProvider
     */
    public function testVkLink($input, $correct)
    {
        $this->assertEquals($correct, $this->lp->link($input));
    }

    public function loginDataProvider(): array
    {
        return [
            [
                'https://vk.com/id1',
                'id1',
            ],
            [
                'https://VK.com/superpuper',
                'superpuper',
            ],
            [
                'https://vkoNtakte.ru/superpuper',
                'superpuper',
            ],
            [
                'https://vk.com/id1asd32',
                'id1asd32',
            ],
            [
                'https://instagram.com/ab',
                null,
            ],
            [
                'some bad long long login 1234567890 and more text here la la la',
                null,
            ],
            [
                'https://vk.ru/superpuper',
                null,
            ],
        ];
    }

    public function linkDataProvider(): array
    {
        return [
            [
                'https://vk.com/oleg',
                'https://vk.com/oleg'
            ],
            [
                'https://vk.com/funfriendsshop?w=story-196230822_456239023%2Fgroup_stories',
                'https://vk.com/story-196230822_456239023'
            ],
            [
                'https://vk.com/id506291422?z=photo506291422_457252907%2Falbum506291422_0%2Frev',
                'https://vk.com/photo506291422_457252907'
            ],
            [
                'https://vkOnTakTe.ru/id506291422?z=photo506291422_457252907%2Falbum506291422_0%2Frev',
                'https://vkOnTakTe.ru/photo506291422_457252907'
            ],
            [
                'https://vk.com/wall16693907_4220',
                'https://vk.com/wall16693907_4220'
            ],
            [
                'https://vk.com/photo472790069_457242622',
                'https://vk.com/photo472790069_457242622'
            ],
            [
                'https://vk.com/club102800900',
                'https://vk.com/club102800900'
            ],
            [
                'http://m.vk.com/club189152741?from=groups',
                'http://m.vk.com/club189152741'
            ],
            [
                'https://m.vk.ru/club189152741?from=groups',
                 null
            ],
            [
                'https://vk.ru/id506291422?z=photo506291422_457252907%2Falbum506291422_0%2Frev',
                null
            ]
        ];
    }
}
