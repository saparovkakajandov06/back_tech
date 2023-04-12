<?php

namespace App\Console\Commands;

use App\Domain\Models\Chunk;
use App\Domain\Services\Everve\Everve;
use App\Domain\Services\Nakrutka\Nakrutka;
use App\Domain\Services\Vkserfing\Vkserfing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class MakeRemoteCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:make_remote_count {pid} {ps}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function handle()
    {
//        $chunks = Chunk::inRandomOrder()->limit(10)->cursor();
//        $chunks = [
//            Chunk::find(123123),
//            Chunk::find(555555),
//            Chunk::find(100200),
//        ];

        $ps = $this->argument('ps');
        $pid = $this->argument('pid');
        $r = $pid - 1;

        $chunks = Chunk::whereRaw("id % $ps = $r")->cursor();
        $total = Chunk::whereRaw("id % $ps = $r")->count();

        $stamp = "[$pid]";
        echo "$stamp starting process $pid of $ps\n";
        echo "$stamp total $total chunks\n";

        $counter = 0;

        foreach ($chunks as $chunk) {
            try {
                $rc = $chunk->getOldRemoteCount();

                $details = $chunk->details;
                $details['remote_count'] = $rc;
                $chunk->details = $details;
                $chunk->save();

                $counter++;
                if ($counter % 10000 === 0) {
                    $progress = round(
                        $counter * 100 / $total, 2, PHP_ROUND_HALF_DOWN);
                    echo "$stamp $progress%\n";
                }
            } catch (\Throwable $e) {
                echo "$stamp Exception with chunk id " . $chunk->id . "\n";
                echo $stamp . ' ' . describe_exception($e);
                echo "\n";
                echo "$stamp saving count -> remote_count\n";

                $details = $chunk->details;
                $details['remote_count'] = $details['count'];
                $chunk->details = $details;
                $chunk->save();
            }
        }

        echo "$stamp done\n";
    }
}
