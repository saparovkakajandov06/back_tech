<?php

namespace App\Exceptions\NonReportable;

use App\Exceptions\TException;
use App\Exceptions\Traits\NonReportable;

class BadCurrencyException extends TException
{
    use NonReportable;

    protected $key = 'exceptions.bad_currency';
}
