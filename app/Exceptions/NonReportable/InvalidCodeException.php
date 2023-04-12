<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\NonReportable;

class InvalidCodeException extends TException {

    use NonReportable;

    protected $key = 'exceptions.invalid_code';
}
