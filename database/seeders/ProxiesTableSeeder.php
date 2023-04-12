<?php

namespace Database\Seeders;

use App\Proxy;
use Illuminate\Database\Seeder;

class ProxiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('proxies')->delete();

        Proxy::create([
            'comment' => 'id 0',
            'url' => 'http://G5D0ba:s7fXWG@45.81.76.81:8000',
            'instagram' => 'shalon_reaume_238:qcasxDNRjdQK',
            'cookie' => 'ig_did=DDAA52C2-38B1-4173-9525-28B141B921A0; mid=X-Q8UwALAAFY67qN5mo0D7w-6yiK; ig_nrcb=1; shbid=6630; shbts=1608816467.6336243; rur=FTW; urlgen="{\\"45.10.80.67\\": 49505\\054 \\"45.81.76.81\\": 35751}:1ksprs:UFQ5lrsyoC2rTskMuMKkLripqBI"; csrftoken=uYzj7Ygt7mQcEDd7FfuaOOsM1BGsPcQv; ds_user_id=457532788; sessionid=457532788%3A6XMYvXV7SOMKHo%3A25',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'id 3',
            'url' => 'http://G5D0ba:s7fXWG@45.81.79.169:8000',
            'instagram' => 'shmidt_z:Bfy6mEysp7X87uE',
            'cookie' => 'ig_did=67561E2E-B828-4E19-AE5E-E548781B1E27; csrftoken=uSOWuDNczyJsUNqexRv3krFsFMsFh9in; mid=X-YS5wALAAHA7bApF1nI2fKpL0D2; ig_nrcb=1; rur=ATN; ds_user_id=6157175529; sessionid=6157175529%3A4UpIfGwMGHvvAJ%3A10; shbid=3597; shbts=1608913655.9187553; urlgen="{\\"45.81.79.169\\": 35751}:1kspwJ:DDinQMieXATYhVFZJnfun9Bayp4"',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'id 8',
            'url' => 'http://G5D0ba:s7fXWG@45.81.78.38:8000',
            'instagram' => 'vasilina.semenova.2017:4PCuxW43xSHrDAS',
            'cookie' => 'ig_did=B4393E04-408D-4B7C-A1AC-1300150282A9; csrftoken=JuCljv4Xv9lFOJolbFq66LJQABcKeJAM; mid=X-YT4wALAAFHk10nRIhzl2aQ5rt4; ig_nrcb=1; rur=RVA; ds_user_id=6157580373; sessionid=6157580373%3AiCmxwb7fUX6zMz%3A20; shbid=9616; shbts=1608913918.492981; urlgen="{\\"45.81.78.38\\": 35751}:1ksq0Z:yXpV9LeYqC3_p96H18fXUHET3Ag"',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'id 12',
            'url' => 'http://gjTEVr:P39aPS@45.10.80.67:8000',
            'instagram' => 'rianrodrigues122:4PCuxW43xSHrDAS',
            'cookie' => 'ig_did=BC0E7AE0-4922-49E0-8D02-6B7960A7FF8D; csrftoken=4CmMkgJ8X1YAH0pZXqb6rk0mLOfLEc7x; mid=X-YULQALAAHLiSKF4qFKgPrsTNf6; ig_nrcb=1; rur=PRN; ds_user_id=464317067; sessionid=464317067%3A3vO9dsqrpTH0gu%3A16; shbid=18621; shbts=1608913982.4623392; urlgen="{\\"45.10.80.67\\": 49505}:1ksq1Y:io91XKcl8N8g_HzGeJo0oGjles8"',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'x1',
            'url' => 'http://G5D0ba:s7fXWG@45.81.78.232:8000',
            'instagram' => 'ksyushakiseleva73:O9rRD5JB281',
            'cookie' => 'ig_cb=2; ig_did=42D8DC03-10CC-46FF-8A97-41E453434CE5; csrftoken=jrYwFMkGAd78hA5wWH6naFYZh8AYzGOS; mid=YAd6dQALAAFMoqJgKowlSwIFfTy5; urlgen="{\"45.81.78.232\": 35751}:1l2S4c:PXdJWbsMYnMJnTjzOFpJxCXvh54"; rur=ASH; ds_user_id=13538015844; sessionid=13538015844%3AMslEsMyfCypENC%3A4; shbid=14494; shbts=1611205184.7308075',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'x2',
            'url' => 'http://G5D0ba:s7fXWG@45.81.77.32:8000',
            'instagram' => 'kirasokolova750:LKt00myE221',
            'cookie' => 'ig_did=06FB4662-8245-4435-BA6E-DE8A13C466EF; csrftoken=qUp3wsdUyvPGNPhjrjaRXt2lWLlyDYKj; mid=YAkM2gALAAELGNeof8w4ATcZAJSy; ig_nrcb=1; urlgen="{\"45.81.77.32\": 35751}:1l2SHu:JO-29Z7uluf8zUOUGBTEvVgEga4"; rur=PRN; ds_user_id=13598099716; sessionid=13598099716%3AmUjFEhhbUPXKgO%3A7; shbid=1886; shbts=1611206006.9159718',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'enabled' => true,
        ]);

        $login = "G5D0ba";
        $pass = "s7fXWG";

        Proxy::create([
            'comment' => 'x3',
            'url' => "http://$login:$pass@45.81.78.76:8000",
            'instagram' => 'evelinapavlova93:uo0U3Sm93T1',
            'cookie' => 'ig_did=F24E5EAE-E248-4A0A-BE62-9E6AD7896156; csrftoken=RLWsyLLpdB3FP6ub2LkTUE4T4SF4Vh0I; mid=YAkOEAALAAGJK6QGIXtDA3h2P2H7; ig_nrcb=1; urlgen="{\"45.81.78.76\": 35751}:1l2SLJ:hIfSwPdNBj68InFJdPV11Myo5iY"; rur=FTW; ds_user_id=13498933982; sessionid=13498933982%3AqVsJs45cbGNc1R%3A17; shbid=15071; shbts=1611206228.242629',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'x4',
            'url' => "http://$login:$pass@45.81.76.64:8000",
            'instagram' => null,
            'cookie' => null,
            'user_agent' => null,
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'x5',
            'url' => "http://$login:$pass@45.81.77.14:8000",
            'instagram' => null,
            'cookie' => null,
            'user_agent' => null,
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'x6',
            'url' => "http://$login:$pass@45.81.78.74:8000",
            'instagram' => null,
            'cookie' => null,
            'user_agent' => null,
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'x7',
            'url' => "$login:$pass@45.81.77.87:8000",
            'instagram' => 'doseof.satisfaction:4PCuxW43xSHrDAS',
            'cookie' => 'ig_did=72484E8E-76E6-4049-BA31-E499D3FC0843; csrftoken=Y9JfNjZwZxjXNkt5uc13x9oH1if97LHT; rur=ATN; mid=YAkWxwALAAHqlXlXTVGoJsssHCXG; ds_user_id=898885436; sessionid=898885436%3AO3rl6TDRQQyw83%3A1; shbid=16811; shbts=1611208394.5747073; urlgen="{\"45.81.77.87\": 35751}:1l2SuS:gsZX52H3XT3fvVwfpt6yUcEYDvM"',
            'user_agent' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0',
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'x8',
            'url' => "http://$login:$pass@45.81.76.222:8000",
            'instagram' => null,
            'cookie' => null,
            'user_agent' => null,
            'enabled' => true,
        ]);

        Proxy::create([
            'comment' => 'x9',
            'url' => "http://$login:$pass@45.81.77.250:8000",
            'instagram' => null,
            'cookie' => null,
            'user_agent' => null,
            'enabled' => true,
        ]);
    }
}
