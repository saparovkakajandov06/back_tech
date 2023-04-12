<?php

namespace App\Console\Commands;

use App\Services\VKTransport;
use Illuminate\Console\Command;
use App\VK\User as VKUser;

class LoadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads vk data';

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
//        echo "Hello! Loading vk data... ok\n";
//        echo "bye\n";


        $diana = new VKUser("81782335");
//        $nadia = new VKUser("329985248");
//
//        echo "diana #" . spl_object_id($diana) . "\n";
//        echo "nadia #" . spl_object_id($nadia) . "\n";

//        echo "diana vk id " . spl_object_id($diana->vk);
//        echo "\n";
//        echo "nadia vk id " . spl_object_id($nadia->vk);
//        echo "\n";


        $diana_friends = $diana->getFriends();

        echo "--- diana's friends ---\n";
        foreach($diana_friends as $friend) {
            $friend->loadNames();
            echo $friend;
        }
        echo "--- end ---";
    }
}
