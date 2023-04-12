<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\NonReportable;

class InsufficientFundsException extends TException {

    use NonReportable;

    protected $key = 'exceptions.insufficient_funds';
}
