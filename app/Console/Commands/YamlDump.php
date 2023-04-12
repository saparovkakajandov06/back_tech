<?php

namespace App\Console\Commands;

use App\UserService;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class YamlDump extends Command
{
    protected $signature = 'yaml:dump';
    protected $description = 'Dump user services to yaml';

    public function __construct()
    {
        parent::__construct();
    }

    protected function dumpModel($service, $dirname)
    {
        if ($service->platform) {
            $dirname = $dirname . '/' . $service->platform;
        }

        if (!is_dir($dirname)) {
            mkdir($dirname);
        }

        $filepath = $dirname . '/' . $service->tag . '.yaml';
        $yaml = Yaml::dump($service->toArray(), 4);

        echo "Saving " . $filepath . "\n";
        file_put_contents($filepath, $yaml);
    }

    public function handle()
    {
        $dirname = 'user_services';

        echo "Dumping to " . $dirname . "\n";

        if (!is_dir($dirname)) {
            mkdir($dirname);
        }

        $services = UserService::all();

        foreach ($services as $service) {
            $this->dumpModel($service, $dirname);
        }

        echo "ok\n";
    }
}
