<?php

namespace App\Console\Commands;

use App\Domain\Models\CompositeOrder;
use Illuminate\Console\Command;

class ProcessOrderCommand extends Command
{
    protected $signature = 'ss:process {uuid}';
    protected $description = 'Run order by uuid';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $uuid = $this->argument('uuid');
        $order = CompositeOrder::where('uuid', $uuid)->firstOrFail();

        $order->run();

        echo "ok";
    }
}
