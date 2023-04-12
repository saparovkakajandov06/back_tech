<?php

namespace App\Console\Commands;

use App\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class YamlLoad extends Command
{
    protected $signature = 'yaml:load {tag?}';
    protected $description = 'Load user services from yaml';

    protected $done;

    public function __construct()
    {
        parent::__construct();
        $this->done = false;
    }

    protected static function loadModel($path)
    {
        $value = Yaml::parseFile($path);
        if ($model = UserService::where('tag', $value['tag'])->first()) {
            $model->update($value);
        } else {
            $model = UserService::create($value);
        }
        return $model;
    }

    protected function load($dir, $tag='')
    {
        if ($this->done) {
            return;
        }

        echo "Scanning $dir\n";
        $cdir = scandir($dir);

        foreach ($cdir as $_ => $filename) {
            if (!in_array($filename, array(".", ".."))) {

                $path = $dir . '/' . $filename;

                if (is_dir($path)) {
                    $this->load($path, $tag);
                } else {
                    if ($tag) {
                        if (Str::contains($path, $tag)) {
                            echo "Loading $path\n";
                            self::loadModel($path);
                            $this->done = true;
                            return;
                        } else {
                            // do nothing
                        }
                    } else {
                        echo "Loading $path\n";
                        self::loadModel($path);
                    }
                }
            }
        }
    }

    public function handle()
    {
        $dirname = 'user_services';
        $tag = $this->argument('tag');
        if ($tag) {
            $this->load($dirname, $tag);
        } else {
            UserService::truncate();
            $this->load($dirname);
        }
    }
}
