<?php

namespace Database\Seeders;

use App\Domain\Models\Labels;
use App\Domain\Models\Slots;
use App\Domain\Services\AbstractService;
use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\Domain\Services\Vtope\AVtope;
use App\Domain\Transformers\General\SetRegion;
use App\Domain\Transformers\Parsers\ParseTiktokLink;
use App\Domain\Transformers\Parsers\ParseTiktokLogin;
use App\Domain\Transformers\SaveImg;
use App\Domain\Transformers\Scrapers\ScrapeTiktokProfile;
use App\Domain\Transformers\Scrapers\ScrapeTiktokVideo;
use App\Domain\Transformers\Scrapers\SetTiktokScraper;
use App\Domain\Transformers\SetDefaultPriceFromCount;
use App\Domain\Transformers\SetOneOrder;
use App\Domain\Validators\CheckHasLinkAndCount;
use App\Domain\Validators\CheckHasLoginAndCount;
use App\UserService;
use App\USPrice;
use Illuminate\Database\Seeder;


class TiktokLightServicesPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $arrayOfServices = array(
            /* |=========================|
                 * | Tiktok                  |
                 * |=========================|
                 * */

            array(
                'tag' => UserService::TIKTOK_LIKES_LIGHT4,
                'RUB' => [
                    "1" => "0.35",
                    "500" => "0.28",
                    "1000" => "0.175",
                    "2500" => "0.228",
                    "5000" => "0.175",
                    "10000" => "0.215",
                    "25000" => "0.175",
                    "50000" => "0.198",
                    "100000" => "0.1775"
                ],
                'EUR' => [
                    "1" => "0.0065",
                    "500" => "0.005",
                    "1000" => "0.00325",
                    "2500" => "0.00449",
                    "5000" => "0.00325",
                    "10000" => "0.002999",
                    "25000" => "0.00325",
                    "50000" => "0.0023998",
                    "100000" => "0.0019999"
                ],
                'USD' => [
                    "1" => "0.0099",
                    "500" => "0.00798",
                    "1000" => "0.00495",
                    "2500" => "0.0076",
                    "5000" => "0.00495",
                    "10000" => "0.007",
                    "25000" => "0.00495",
                    "50000" => "0.0059",
                    "100000" => "0.00525"
                ],
                'TRY' => [
                    "1" => 0,
                    "500" => 0,
                    "1000" => 0,
                    "2500" => 0,
                    "5000" => 0,
                    "10000" => 0,
                    "25000" => 0,
                    "50000" => 0,
                    "100000" => 0
                ],
                'UAH' => [
                    "1" => 0,
                    "500" => 0,
                    "1000" => 0,
                    "2500" => 0,
                    "5000" => 0,
                    "10000" => 0,
                    "25000" => 0,
                    "50000" => 0,
                    "100000" => 0
                ],
                'sale' => null
            ),

            array (
                'tag' => UserService::TIKTOK_SUBS_LIGHT4,
                'RUB' => [
                    "1" => "0.75",
                    "500" => "0.56",
                    "1000" => "0.375",
                    "2500" => "0.524",
                    "5000" => "0.375",
                    "10000" => "0.485",
                    "25000" => "0.375",
                    "50000" => "0.45",
                    "100000" => "0.39"
                ],
                'EUR' => [
                    "1" => "0.0199",
                    "500" => "0.0149",
                    "1000" => "0.00999",
                    "2500" => "0.014",
                    "5000" => "0.00999",
                    "10000" => "0.0129",
                    "25000" => "0.00999",
                    "50000" => "0.01194",
                    "100000" => "0.0105"
                ],
                'USD' => [
                    "1" => "0.0199",
                    "500" => "0.0149",
                    "1000" => "0.00999",
                    "2500" => "0.014",
                    "5000" => "0.00999",
                    "10000" => "0.0129",
                    "25000" => "0.00999",
                    "50000" => "0.01194",
                    "100000" => "0.0105"
                ],
                'TRY' => [
                    "1" => 0,
                    "500" => 0,
                    "1000" => 0,
                    "2500" => 0,
                    "5000" => 0,
                    "10000" => 0,
                    "25000" => 0,
                    "50000" => 0,
                    "100000" => 0
                ],
                'UAH' => [
                    "1" => 0,
                    "500" => 0,
                    "1000" => 0,
                    "2500" => 0,
                    "5000" => 0,
                    "10000" => 0,
                    "25000" => 0,
                    "50000" => 0,
                    "100000" => 0
                ],
                'sale' => [
                    "500" => true,
                    "1000" => false,
                    "5000" => false,
                    "10000" => true,
                    "25000" => false,
                    "50000" => true,
                    "100000" => false
                ]
            ),

            array (
                'tag' => UserService::TIKTOK_VIEWS_LIGHT4,
                'RUB' => [
                    "1" => "0.15",
                    "500" => "0.12",
                    "1000" => "0.075",
                    "2500" => "0.098",
                    "5000" => "0.075",
                    "10000" => "0.09",
                    "25000" => "0.075",
                    "50000" => "0.085",
                    "100000" => "0.065"
                ],
                'EUR' => [
                    "1" => "0.0065",
                    "500" => "0.00598",
                    "1000" => "0.00325",
                    "2500" => "0.00449",
                    "5000" => "0.00325",
                    "10000" => "0.002999",
                    "25000" => "0.00325",
                    "50000" => "0.0023998",
                    "100000" => "0.0019999"
                ],
                'USD' => [
                    "1" => "0.0065",
                    "500" => "0.00598",
                    "1000" => "0.00325",
                    "2500" => "0.00449",
                    "5000" => "0.00325",
                    "10000" => "0.002999",
                    "25000" => "0.00325",
                    "50000" => "0.0023998",
                    "100000" => "0.0019999"
                ],
                'TRY' => [
                    "1" => 0,
                    "500" => 0,
                    "1000" => 0,
                    "2500" => 0,
                    "5000" => 0,
                    "10000" => 0,
                    "25000" => 0,
                    "50000" => 0,
                    "100000" => 0
                ],
                'UAH' => [
                    "1" => 0,
                    "500" => 0,
                    "1000" => 0,
                    "2500" => 0,
                    "5000" => 0,
                    "10000" => 0,
                    "25000" => 0,
                    "50000" => 0,
                    "100000" => 0
                ],
                'sale' => null
            ),
        );

        foreach($arrayOfServices as $service) {
            USPrice::where('tag', $service['tag'])->delete();
            USPrice::create($service);
        }
    }
}
