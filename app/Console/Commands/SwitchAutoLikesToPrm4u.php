<?php

namespace App\Console\Commands;

use App\Domain\Services\Prm4uAuto\APrm4uAuto;
use App\Domain\Splitters\Prm4uAutoOneSplitter;
use Illuminate\Console\Command;
use US;

class SwitchAutoLikesToPrm4u extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:switch_auto_likes_to_prm4u';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes use of newly creates Prm4uAuto service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = [
            US::INSTAGRAM_AUTO_LIKES_LK => [
                'splitter' => Prm4uAutoOneSplitter::class,
                'config' => [[
                    'name' => 'INSTAGRAM_AUTO_LIKES_LK_PRM4U_AUTO',
                    'service_class' => APrm4uAuto::class,
                    'order' => 1,
                    'min' => 100,
                    'max' => 50000,
                    'remote_params' => [
                        'service' => 478,
                        'delay' => 0
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 3.7,
                        'mode' => 'auto',
                        'auto' => 0,
                        'auto_timestamp' => null
                    ],
                    'count_extra_percent' => 0,
                    'isEnabled' => true
                ]]
            ],
            US::INSTAGRAM_AUTO_LIKES_MAIN => [
                'splitter' => Prm4uAutoOneSplitter::class,
                'config' => [[
                    'name' => 'INSTAGRAM_AUTO_LIKES_LK_PRM4U_AUTO',
                    'service_class' => APrm4uAuto::class,
                    'order' => 1,
                    'min' => 100,
                    'max' => 10000,
                    'remote_params' => [
                        'service' => 478,
                        'delay' => 0
                    ],
                    'net_cost' => [
                        'amount' => 100,
                        'local' => 1.4856,
                        'mode' => 'auto',
                        'auto' => 0,
                        'auto_timestamp' => null
                    ],
                    'count_extra_percent' => 0,
                    'isEnabled' => true
                ]]
            ]
        ];

        echo 'Introducing new service provider: Prm4uAuto!' . PHP_EOL;

        foreach ($data as $tag => $fields) {
            $userService = US::where('tag', $tag)->firstOrFail();
            $userService->update($fields);
            echo "$tag now works with Prm4uAuto." . PHP_EOL;
        }
        echo PHP_EOL . 'done' . PHP_EOL;

        return 0;
    }
}
