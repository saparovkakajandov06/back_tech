<?php

namespace App\Documentor;

use App\Exceptions\NonReportable\AttributeException;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Verbs implements Single
{
    private array $verbs;

    public function __construct(...$verbs)
    {
        foreach ($verbs as $verb) {
            if (! in_array($verb, Documentor::VERBS)) {
                throw new AttributeException('Bad verb: ' . $verb);
            }
        }
        $this->verbs = $verbs;
    }

    public function getKey(): string
    {
        return 'verbs';
    }

    public function getValue(): mixed
    {
        return $this->verbs;
    }
}
