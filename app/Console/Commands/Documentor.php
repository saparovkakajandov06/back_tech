<?php

namespace App\Console\Commands;

use App\Documentor\DocumentorService;
use Illuminate\Console\Command;

class Documentor extends Command
{
    protected $signature = 'smm:documentor';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $svc = new DocumentorService();
        echo json_encode($svc->getData(), JSON_PRETTY_PRINT);
        echo "\n";
    }
}
