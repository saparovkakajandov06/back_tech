<?php

namespace App\Console\Commands;

use App\Services\EventsSchemesService;
use Illuminate\Console\Command;

class CheckSendpulseEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smm:check_sendpulse_events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check other';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    public function handle(EventsSchemesService $ess)
    {
        $ess->checkEventForSend();

    }
}