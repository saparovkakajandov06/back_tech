<?php

namespace Tests\TF;

use Faker\Factory;
use Illuminate\Support\Str;

//.-._   _ _ _ _ _ _ _ _
//.-''-.__.-'00  '-' ' ' ' ' ' ' ' '-.
//'.___ '    .   .--_'-' '-' '-' _'-' '._
// V: V 'vv-'   '_   '.       .'  _..' '.'.
//'=.____.=_.--'   :_.__.__:_   '.   : :
//           (((____.-'        '-.  /   : :
//                             (((-'\ .' /
//                           _____..'  .'
//                          '-._____.-'

class Gena
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function login(): string
    {
        $name = explode(' ', $this->faker->name);
        $name = join('_', $name);
        $name = mb_strtolower($name);

        return $name;
    }

    public function email(): string
    {
        return Str::lower(Str::random(8)) . '@smmtouch.store';
    }

    public function password(): string
    {
        return Str::random(8);
    }
}
