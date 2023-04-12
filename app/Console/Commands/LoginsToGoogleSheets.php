<?php

namespace App\Console\Commands;

use App\Services\LoginStats;
use Illuminate\Console\Command;


class LoginsToGoogleSheets extends Command
{
    protected $signature = 'smm:logins_to_google_sheets';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(LoginStats $stats)
    {
        $stats->dataUp();
    }
}
