<?php

namespace App\Console\Commands;

use App\Domain\Models\CompositeOrder;
use Illuminate\Console\Command;

class ScrapeOrderCommand extends Command
{
    protected $signature = 'ss:scrape {uuid}';
    protected $description = 'Scrape order by uuid';

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
