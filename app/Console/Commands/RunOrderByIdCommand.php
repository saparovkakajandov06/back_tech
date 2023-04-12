<?php

namespace App\Console\Commands;

use App\Domain\Models\CompositeOrder;
use Illuminate\Console\Command;

class RunOrderByIdCommand extends Command
{
    protected $signature = 'ss:run {id}';
    protected $description = 'Run order by id';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $id = $this->argument('id');
        $order = CompositeOrder::where('id', $id)->firstOrFail();

        $order->run();

        echo "ok";
    }
}
