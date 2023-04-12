<?php

namespace App\Documentor;

use App\Exceptions\NonReportable\AttributeException;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Endpoint implements Single
{
    private string $path;

    public function __construct(string $path)
    {
        if (empty($path)) {
            throw new AttributeException('Endpoint has no path');
        }
        $this->path = $path;
    }

    public function getKey(): string
    {
        return 'endpoint';
    }

    public function getValue(): mixed
    {
        return $this->path;
    }
}
