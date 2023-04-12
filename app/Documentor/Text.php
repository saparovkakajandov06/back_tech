<?php

namespace App\Documentor;

use App\Exceptions\NonReportable\AttributeException;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Text implements Plural
{
    private string $text;

    public function __construct($text)
    {
        if (empty($text)) {
            throw new AttributeException('Empty text');
        }

        $this->text = $text;
    }

    public function getKey()
    {
        return 'text';
    }

    public function getValue()
    {
        return $this->text;
    }
}
