<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\NonReportable;

class MissingParameterException extends TException
{
    use NonReportable;

    protected $key = 'exceptions.missing_parameter';
}
