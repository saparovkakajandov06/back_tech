<?php

namespace App\Documentor;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Description
{
    private ?string $description;

    public function __construct($description)
    {
        $this->description = $description;
    }

    public function getKey()
    {
        return 'description';
    }

    public function getValue()
    {
        return $this->description;
    }
}
