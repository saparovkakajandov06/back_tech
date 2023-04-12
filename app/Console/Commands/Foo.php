<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Foo extends Command
{
    protected $signature = 'smm:foo';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Artisan::call('smm:say one');
        Artisan::call('smm:say two');
    }
}
