<?php

namespace App\Console\Commands;

use App\Services\UpdateService;
use Illuminate\Console\Command;

class XActive extends Command
{
    protected $signature = 'x:active {limit}';

    public UpdateService $updateService;

    public function __construct()
    {
        parent::__construct();
        $this->updateService = new UpdateService();
    }

    public function handle()
    {
        echo $this->updateService
                  ->getOrderIdsForUpdate($this->argument('limit'))
                  ->join(' ');
    }
}
