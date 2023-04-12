<?php

namespace App\Exceptions\Reportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\Reportable;

class ParserException extends TException
{
    use Reportable;

    protected $key = 'exceptions.parser';
}
