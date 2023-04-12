<?php

namespace App\Console\Commands;

use App\Services\ProfilerService;
use Illuminate\Console\Command;

class Say extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:say {text}';

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
    public function handle(ProfilerService $prf)
    {
        $prf->start('one');

            $prf->start('second');
            sleep(1);
            $prf->stop('second');

            sleep(1);

            $prf->start('second');
            sleep(1);
            $second = $prf->stop('second');

            $prf->start('third');
            $text = $this->argument('text');
            for($i = 0; $i < 3; $i++) {
                sleep(1);
                echo $text . "\n";
            }
            $third = $prf->stop('third');


        echo "second time: " . $second . "\n";
        echo "third time: " . $third . "\n";
        echo "total time: " . $prf->stop('one') . "\n";
    }
}
