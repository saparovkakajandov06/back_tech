<?php

namespace Tests\Unit;

use App\Util;
use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testGetInstagramLoginFromUrl()
    {
        $url = 'https://www.instagram.com/chinesekitchenlbk/';
        $login = Util::getInstagramLoginFromUrl($url);
        $this->assertEquals('chinesekitchenlbk', $login);
    }

    public function testGetInstagramLoginFromUrl2()
    {
        $url = 'http://www.instagram.com/login';
        $login = Util::getInstagramLoginFromUrl($url);
        $this->assertEquals('login', $login);
    }

    public function testGetInstagramLoginFromUrl3()
    {
        $url = 'http://www.instagram.com/login/and_some_data/once_more';
        $login = Util::getInstagramLoginFromUrl($url);
        $this->assertEquals('login', $login);
    }

    public function testGetInstagramLoginFromUrl4()
    {
        $url = 'http://www.instagram.com/login?trash1=one&trash2=two';
        $login = Util::getInstagramLoginFromUrl($url);
        $this->assertEquals('login', $login);
    }

    public function testShouldReturnLogin()
    {
        $login = Util::parseInstagramLogin('login');
        $this->assertEquals('login', $login);
    }

    public function testShouldParseUrl()
    {
        $url = 'http://www.instagram.com/login?trash1=one&trash2=two';
        $login = Util::parseInstagramLogin($url);
        $this->assertEquals('login', $login);
    }
}
