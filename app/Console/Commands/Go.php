<?php

namespace App\Console\Commands;

use App\Domain\Services\Everve\Everve;
use App\Domain\Services\Nakrutka\Nakrutka;
use App\Domain\Services\Vkserfing\Vkserfing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class Go extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        echo "go\n";

//        echo "vkserfing many orders --\n";
//        $s = App::make(Vkserfing::class);
//        $ss = $s->getManyStatuses([809551, 809558]);
//        foreach ($ss as $status) {
//            echo $status->d();
//            echo "\n";
//        }
//
//        echo "vkserfing one order --\n";
//        $one = $s->getStatus(809551);
//        echo $status->d();

//        echo "nakrutka many orders --\n";
//        $s = App::make(Nakrutka::class);
//        $ss = $s->getManyStatuses([1259970, 1259981]);
//        foreach ($ss as $status) {
//            echo $status->d();
//            echo "\n";
//        }
//
//        echo "nakrutka one order --\n";
//        $one = $s->getStatus(1259970);
//        echo $one->d();


        echo "everve many orders --\n";
        $s = App::make(Everve::class);
        $ss = $s->getManyStatuses([160590, 160588, 160587]);
        foreach ($ss as $status) {
            echo $status->d();
            echo "\n";
        }

        echo "everve one order --\n";
        $one = $s->getStatus(160584);
        echo $one->d();
    }
}
