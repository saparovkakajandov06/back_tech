<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\NonReportable;

class InvalidPasswordException extends TException {

    use NonReportable;

    protected $key = 'exceptions.invalid_password';
}
