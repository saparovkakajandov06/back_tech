<?php

namespace App\Console\Commands;

use App\Domain\Models\Slots;
use App\Domain\Services\VkserfingAuto\AVkserfingAuto;
use Illuminate\Console\Command;
use US;

class VkFix extends Command
{
    protected $signature = 'smm:vk_fix';

    protected $description = 'fix 2 vkserfing services vk -> vkauto 30 july 2021';

    public function handle()
    {
        $s = US::where('tag', US::VK_AUTO_LIKES_LK)->firstOrFail();
        $c = $s->config;
        data_set($c, '0.name', Slots::VK_AUTO_LIKES_LK_VKSERFING_AUTO);
        data_set($c, '0.service_class', AVkserfingAuto::class);
        $s->config = $c;
        $s->save();

        $s = US::where('tag', US::VK_AUTO_LIKES_MAIN)->firstOrFail();
        $c = $s->config;
        data_set($c, '0.name', Slots::VK_AUTO_LIKES_MAIN_VKSERFING_AUTO);
        data_set($c, '0.service_class', AVkserfingAuto::class);
        $s->config = $c;
        $s->save();

//        Slots::mergeSlotConfig(Slots::VK_AUTO_LIKES_LK_VKSERFING, [
//            [
//                'name' => Slots::VK_AUTO_LIKES_LK_VKSERFING_AUTO,
//                'service_class' => AVkserfingAuto::class,
//            ],
//        ]);
//
//        Slots::mergeSlotConfig(Slots::VK_AUTO_LIKES_MAIN_VKSERFING, [
//            [
//                'name' => Slots::VK_AUTO_LIKES_MAIN_VKSERFING_AUTO,
//                'service_class' => AVkserfingAuto::class,
//            ],
//        ]);

        echo "Done.\n";
    }
}
