<?php

namespace App\Documentor;

use App\Exceptions\NonReportable\AttributeException;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Group implements Single
{
    private string $name;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new AttributeException('Group has no name');
        }
        $this->name = $name;
    }

    public function getKey(): string
    {
        return 'group';
    }

    public function getValue(): mixed
    {
        return $this->name;
    }
}
