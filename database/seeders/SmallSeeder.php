<?php

namespace Database\Seeders;

use App\Domain\Models\Slots;
use App\Domain\Services\Fake\AFake;
use App\UserService;
use Illuminate\Database\Seeder;

class SmallSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('user_services')->delete();

//        \DB::table('user_services')->insert(array(

        $arrayOfServices = array(

            array(
                'id' => 64,
                'title' => 'Fake Service Title',
                'tag' => UserService::FAKE_SERVICE_LK,
                'splitter' => 'App\\Domain\\Splitters\\DefaultSplitter',
                'config' => [
                    [
                        'name' => Slots::FAKE_SERVICE_LK_FAKE_1,
                        'service_class' => AFake::class,
                        'order' => 1,
                        "min" => 1,
                        "max" => 1000,
                        "target" => "target1"
                    ],
                    [
                        'name' => Slots::FAKE_SERVICE_LK_FAKE_2,
                        'service_class' => AFake::class,
                        "order" => 2,
                        "min" => 1,
                        "max" => 1000,
                        "target" => "target2",
                    ],
                ],
                'img' => NULL,
                'created_at' => '2021-05-02 21:56:55+03',
                'updated_at' => '2021-06-07 22:33:59+03',
                'description' => NULL,
                'card' => NULL,
                'local_validation' => NULL,
                'local_checker' => NULL,
                'tracker' => NULL,
                'platform' => 'Fake',
                'name' => NULL,
                'pipeline' => '["App\\\\Domain\\\\Transformers\\\\General\\\\SetRegion", "App\\\\Domain\\\\Validators\\\\CheckHasLinkAndCount", "App\\\\Domain\\\\Validators\\\\CheckHasTargets", "App\\\\Domain\\\\Transformers\\\\SetOneOrder", "App\\\\Domain\\\\Transformers\\\\SetDefaultPriceFromCount", "App\\\\Domain\\\\Validators\\\\CheckUserHasEnoughFunds"]',
                'labels' => ["TYPE_TEST", "DISCOUNT_TEST", "VISIBLE", "ENABLED", "CLIENT_LK"],
            )
        );

        foreach($arrayOfServices as $service) {
            UserService::create($service);
        }
    }
}
