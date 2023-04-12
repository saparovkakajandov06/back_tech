<?php

namespace App\Services;

use App\Domain\Interfaces\IDiscoverable;
use Composer\Autoload\ClassMapGenerator;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

class ServicesService
{
    protected $services;

    public function __construct()
    {
        $classMap = ClassMapGenerator::createMap(base_path() . '/app/Domain/Services');

        foreach ($classMap as $class => $path) {
            try {
                $ref = new ReflectionClass($class);
            } catch (\ReflectionException $e) {
                Log::info('Reflection exception ' . $e->getMessage());
            }

            if ($ref->isAbstract()
                && $ref->implementsInterface(IDiscoverable::class)
            ) {
                $this->services[] = $class;
            }
        }
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getServiceByTag($tag)
    {
        foreach ($this->services as $serviceClass) {
            if ($serviceClass::TAG === $tag) {
                return $serviceClass;
            }
        }

        return null;
    }

    public function getServicesWithMethod($methodName)
    {
        $services = [];

        foreach ($this->services as $serviceClass) {
            if (method_exists($serviceClass, $methodName)) {
                $services[] = $serviceClass;
            }
        }

        return $services;
    }
}
