<?php

namespace App\Documentor;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Param implements Plural
{
    private string $name;
    private ?bool $required;
    private ?string $type;
    private ?string $descr;
    private ?string $example;

    public function __construct($name, $required = false, $type = null, $descr = null, $example = null)
    {
        $this->name = $name;
        $this->required = $required;
        $this->type = $type;
        $this->descr = $descr;
        $this->example = $example;
    }

    public function getKey()
    {
        return 'params';
    }

    public function getValue()
    {
        return [
            'name' => $this->name,
            'required' => $this->required,
            'type' => $this->type ?? null,
            'description' => $this->descr ?? null,
            'example' => $this->example ?? null,
        ];
    }
}
