<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\Traits\NonReportable;
use App\Exceptions\TException;

class AttributeException extends TException
{
    use NonReportable;

    protected $key = 'exceptions.bad_attribute';
}
