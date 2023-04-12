<?php

namespace App\Console\Commands;

use App\Services\UpdateService;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class XMaster extends Command
{
    protected $signature = 'x:master {timeout} {limit} {ps}';

    public UpdateService $updateService;
    public $completed;

    public function __construct()
    {
        parent::__construct();
        $this->updateService = new UpdateService();
        $this->completed = 0;
    }

    public function handle()
    {
        $timeout = $this->argument('timeout');
        $limit = $this->argument('limit');
        $ps = $this->argument('ps');

        $runningOrders = $this->updateService
                              ->getOrderIdsForUpdate($limit);

        if (empty($runningOrders)) {
            echo "No running orders\n";
            return;
        }

        $processes = collect();

        $parts = $runningOrders->split($ps);
        foreach ($parts as $part) {
            $ids = $part->join(' ');
            $cmd = "php artisan x:update $timeout $ids\n";
            echo $cmd;

            $process = Process::fromShellCommandline(command: $cmd, timeout: 180);
            $process->start();

            $processes->push($process);
        }

        while(true) {
            if ($this->completed >= $ps) {
                break;
            }

            sleep(1);

            $processes->each(function ($p) {
                foreach ($p as $type => $data) {
                    if ($p::OUT === $type) {
                        echo $data."\n";
                    } else {
                        echo "[ERR] ".$data."\n";
                    }
                }

                if (! $p->isRunning()) {
                    $this->completed++;
                }
            });
        }
    }
}
