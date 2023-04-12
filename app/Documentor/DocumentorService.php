<?php

namespace App\Documentor;

use Composer\Autoload\ClassMapGenerator;
use ReflectionClass;

class DocumentorService
{
    private function classReflections()
    {
        $p = '/app/Http/Controllers';
//        $p = '/app';
        $classMap = ClassMapGenerator::createMap(base_path() . $p);
        foreach ($classMap as $class => $path) {
            try {
                $reflectionClass = new ReflectionClass($class);
                yield $reflectionClass;
            } catch (\ReflectionException $e) {
                echo $e->getMessage();
            } catch (\ErrorException $e) {
                echo $e->getMessage();
            }
        }
    }

    private function isEndpoint(\ReflectionMethod $method): bool
    {
        $attributes = $method->getAttributes(Endpoint::class);
        return (count($attributes) > 0);
    }

    private function getEndpoints()
    {
        foreach ($this->classReflections() as $class) {
            foreach ($class->getMethods() as $method) {
                if ($this->isEndpoint($method)) {
                    yield $method;
                }
            }
        }
    }

    private function getListOfEndpoints()
    {
        $listOfEndpoints = [];
        $id = 1;

        foreach ($this->getEndpoints() as $method) {

            $item = [ 'id' => $id++ ];

            foreach ($method->getAttributes() as $attr) {

                $inst = $attr->newInstance();

                if ($inst instanceof Single) {
                    $item[$inst->getKey()] = $inst->getValue();
                } elseif ($inst instanceof Plural) {
                    $item[$inst->getKey()][] = $inst->getValue();
                }
            }

            $listOfEndpoints[] = $item;
        }

        return $listOfEndpoints;
    }

    public function getData()
    {
        $start = microtime(true);
        $endpoints = $this->getListOfEndpoints();
        $time_elapsed_secs = microtime(true) - $start;

        return [
            'endpoints' => $endpoints,
            'seconds' => $time_elapsed_secs,
        ];
    }

// $attrName = $attr->getName();
// var_dump($attr->newInstance());
// $attr->getArguments();
}
